#!/usr/bin/env bash
#
# fix-graphify-hook.sh — Perbaiki hook post-commit/post-checkout graphify.
#
# Masalah: hook graphify (di .git/hooks/) mencari interpreter Python yang bisa
# `import graphify` lewat beberapa probe. Di Windows dengan path ber-spasi
# (mis. "C:\Users\Ardan Setiawan\...") probe gagal:
#   - `graphify hook install` menolak path ber-spasi saat menulis _PINNED.
#   - graphify-out/.graphify_python juga difilter allowlist tanpa spasi.
#
# Solusi: script ini menemukan path Python graphify secara otomatis (mendukung
# Windows/Git-Bash/MSYS, WSL, dan Linux; juga lokasi uv tool & pipx), lalu
# menulisnya ke _PINNED di kedua hook. Probe _PINNED runtime memakai quote,
# jadi path ber-spasi AMAN dijalankan oleh Git sh.
#
# Pemakaian (sekali per mesin):
#   Windows (Git Bash) :  bash scripts/fix-graphify-hook.sh
#   WSL                :  bash scripts/fix-graphify-hook.sh
#   Linux (Zorin)      :  bash scripts/fix-graphify-hook.sh
#
# Setelah `uv tool upgrade graphifyy` memindahkan folder, jalankan ulang.
#
set -eu

PROBE="import importlib.util, sys; sys.exit(0 if importlib.util.find_spec('graphify') else 1)"

# ---------------------------------------------------------------------------
# Konversi path Windows -> bentuk yang bisa dicek/dijalankan oleh shell saat ini.
# Di MSYS "C:\...\a b\py.exe" & "C:/.../a b/py.exe" sama-sama valid untuk -x & exec.
# ---------------------------------------------------------------------------
normalize() {
    # ganti backslash -> forward
    printf '%s' "$1" | tr '\\' '/'
}

# ---------------------------------------------------------------------------
# Bentuk path yang ditulis KE HOOK. Hook di Windows dijalankan oleh Git-for-
# Windows (MSYS sh) / Git Bash => harus bentuk "C:/Users/..." (bukan /mnt/c/...).
# Script ini boleh dijalankan dari WSL (yang melihat path sebagai /mnt/c/...).
# ---------------------------------------------------------------------------
to_hook_form() {
    local p="$1"
    case "$(uname -s 2>/dev/null)" in
        *MINGW*|*MSYS*) printf '%s' "$p" ;;
        Linux*)
            # WSL? (/mnt/<drive>/...)
            case "$p" in
                /mnt/[A-Za-z]/*)
                    local drive rest
                    drive="$(printf '%s' "$p" | cut -d/ -f3)"
                    rest="$(printf '%s' "$p" | cut -d/ -f4-)"
                    printf '%s:/%s' "$(printf '%s' "$drive" | tr '[:lower:]' '[:upper:]')" "$rest"
                    ;;
                *) printf '%s' "$p" ;;
            esac
            ;;
        *) printf '%s' "$p" ;;
    esac
}

# ---------------------------------------------------------------------------
# Mencoba sebuah python: wajib executable dan bisa import graphify.
#   use="$1" -> path yang akan dipakai kalau valid
# ---------------------------------------------------------------------------
try_python() {
    local py="$1"
    [ -n "$py" ] || return 1
    py="$(normalize "$py")"
    if [ -x "$py" ] && "$py" -c "$PROBE" 2>/dev/null; then
        printf '%s' "$py"
        return 0
    fi
    return 1
}

find_python() {
    local py result

    # env override
    if [ -n "${GRAPHIFY_PYTHON:-}" ]; then
        if result="$(try_python "$GRAPHIFY_PYTHON")"; then
            echo "$result"; return 0
        fi
    fi

    # Windows: APPDATA (tersedia di MSYS/Git Bash).
    if [ -n "${APPDATA:-}" ]; then
        if result="$(try_python "${APPDATA}/uv/tools/graphifyy/Scripts/python.exe")"; then
            echo "$result"; return 0
        fi
        if result="$(try_python "${APPDATA}/../Local/uv/tools/graphifyy/Scripts/python.exe")"; then
            echo "$result"; return 0
        fi
    fi

    # Windows (WSL): baca APPDATA via cmd.exe bila env kosong.
    if [ -z "${APPDATA:-}" ] && command -v cmd.exe >/dev/null 2>&1; then
        local apd drive rest
        apd="$(cmd.exe /c 'echo %APPDATA%' 2>/dev/null | tr -d '\r\n' || true)"
        if [ -n "$apd" ]; then
            apd="$(normalize "$apd")"
            # di WSL, C:/Users/... -> /mnt/c/Users/... agar dapat dieksekusi bash WSL
            case "$(uname -s 2>/dev/null)" in
                Linux*)
                    case "$apd" in
                        [A-Za-z]:/*)
                            drive="$(printf '%s' "$apd" | cut -d: -f1)"
                            rest="${apd#*:}"
                            apd="/mnt/$(printf '%s' "$drive" | tr '[:upper:]' '[:lower:]')${rest}"
                            ;;
                    esac
                    ;;
            esac
            if result="$(try_python "${apd}/uv/tools/graphifyy/Scripts/python.exe")"; then
                echo "$result"; return 0
            fi
        fi
    fi

    # Linux: uv tool install.
    for p in \
        "${HOME}/.local/share/uv/tools/graphifyy/bin/python" \
        "${HOME}/.local/uv/tools/graphifyy/bin/python" \
        "${HOME}/.local/bin/graphifyy"; do
        if result="$(try_python "$p")"; then
            echo "$result"; return 0
        fi
    done

    # pipx venvs.
    for base in "${PIPX_HOME:-}" "${HOME}/.local/pipx" "${HOME}/.local/share/pipx"; do
        [ -n "$base" ] || continue
        for p in "$base"/venvs/*/bin/python "$base"/venvs/*/Scripts/python.exe; do
            if result="$(try_python "$p")"; then
                echo "$result"; return 0
            fi
        done
    done

    # python3 / python di PATH.
    for p in python3 python; do
        if command -v "$p" >/dev/null 2>&1 && result="$(try_python "$(command -v "$p")")"; then
            echo "$result"; return 0
        fi
    done

    return 1
}

# ---------------------------------------------------------------------------
# Patch nilai _PINNED sebuah hook. value memakai quote langsung (aman spasi).
# ---------------------------------------------------------------------------
patch_pinned() {
    local hook="$1" python="$2" pattern prev

    [ -f "$hook" ] || { echo "  skip (tidak ada): $hook"; return; }
    pattern='^[[:space:]]*_PINNED=.*'
    prev="$(grep -E "$pattern" "$hook" || true)"
    if grep -qE "$pattern" "$hook"; then
        # delimiter '|' -> aman terhadap '/' di path
        sed -i.bak -E "s|^([[:space:]]*_PINNED=).*|\1'$python'|" "$hook" && rm -f "$hook.bak"
        echo "  patched: $hook"
        echo "    $prev  ->  _PINNED='$python'"
    else
        echo "  warning: _PINNED tidak ditemukan di $hook"
    fi
}

# ---------------------------------------------------------------------------
main() {
    local repo_root hook_commit hook_checkout python_file python found

    echo "== fix-graphify-hook =="
    echo "uname: $(uname -s 2>/dev/null || echo '?')"

    if ! found="$(find_python)"; then
        echo "ERROR: tidak menemukan Python yang bisa import 'graphify'." >&2
        echo "Jalankan 'uv tool install graphifyy' / 'pipx install graphifyy' dulu, lalu ulangi." >&2
        exit 1
    fi
    python="$found"

    echo "Python graphify ditemukan: $python"
    echo "  versi: $("$python" -c 'import graphify; print(getattr(graphify, "__version__", "?"))' 2>/dev/null || echo "?")"

    # Bentuk path yang kompatibel dengan shell yang MENJALANKAN hook
    # (Git-for-Windows/Git Bash di Windows memakai "C:/...", bukan /mnt/c/...).
    python="$(to_hook_form "$python")"
    echo "Dipakai di hook: $python"

    repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
    hook_commit="${repo_root}/.git/hooks/post-commit"
    hook_checkout="${repo_root}/.git/hooks/post-checkout"

    echo "Mem-patch hooks:"
    patch_pinned "$hook_commit" "$python"
    patch_pinned "$hook_checkout" "$python"

    # File pembantu (membantu mesin tanpa spasi; mesin ber-spasi di-ignore allowlist hook).
    python_file="${repo_root}/graphify-out/.graphify_python"
    if [ -d "${repo_root}/graphify-out" ]; then
        printf '%s' "$python" > "$python_file"
        echo "Ditulis: $python_file"
    else
        echo "  skip: graphify-out/ belum ada."
    fi

    echo "== Selesai. Uji dengan commit kecil, atau: bash \"$hook_commit\" =="
}

main "$@"