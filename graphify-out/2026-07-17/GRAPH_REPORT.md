# Graph Report - .  (2026-07-17)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 5635 nodes · 18203 edges · 176 communities (170 shown, 6 thin omitted)
- Extraction: 85% EXTRACTED · 15% INFERRED · 0% AMBIGUOUS · INFERRED: 2673 edges (avg confidence: 0.73)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b1a09a3a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- code-editor.js
- chart.js
- rich-editor.js
- markdown-editor.js
- l
- chart.js
- constructor
- resolve
- W
- y
- fromObject
- get
- draw
- .slice
- updateElements
- _update
- create
- lineAt
- constructor
- _update
- id
- facet
- oi
- tables.js
- r
- E
- inRange
- constructor
- advance
- support.js
- Ke
- .append
- Filament\Schemas\Schema
- eq
- o
- Filament\Tables\Table
- notifications.js
- reduce
- slice
- marks
- draw
- file-upload.js
- updateElements
- _notify
- sliceDoc
- slider.js
- find
- isHorizontal
- Illuminate\Database\Eloquent\Model
- N
- Xt
- ManageLeaves
- select.js
- autoload-dev
- Ue
- select.js
- Xt
- echo.js
- Vn
- Cn
- replace
- qt
- wc
- getDatasetMeta
- SupplierResource
- Y
- parse
- fn
- getDatasetMeta
- parse
- app.js
- Illuminate\Database\Eloquent\Builder
- TaskKeluarBarangResource.php
- TaskTerimaSupplierResource.php
- TaskReturCabangResource.php
- TaskIdGenerator
- User.php
- toString
- devDependencies
- render
- ExpeditionResource
- MasterKendaraanResource
- MasterSopirResource
- MasterTokoResource
- e
- Filament\Resources\Pages\CreateRecord
- scripts
- color-picker.js
- En
- render
- Login.php
- t
- te
- selectOption
- renderOptions
- selectOption
- renderOptions
- Symfony\Component\HttpFoundation\Response
- TaskKirimanMobilResource.php
- composer.json
- r
- _e
- oe
- addInner
- Rn
- actions.js
- e
- schemas.js
- require-dev
- setup
- config
- require
- TestCase
- e
- actions.js
- AppServiceProvider
- psr-4
- post-create-project-cmd
- ExampleTest
- extra
- graphify.js
- Controller.php

## God Nodes (most connected - your core abstractions)
1. `o()` - 234 edges
2. `l()` - 205 edges
3. `r()` - 186 edges
4. `t()` - 164 edges
5. `i()` - 163 edges
6. `constructor()` - 146 edges
7. `update()` - 142 edges
8. `h()` - 138 edges
9. `u()` - 132 edges
10. `resolve()` - 97 edges

## Surprising Connections (you probably didn't know these)
- `[x]()` --indirect_call--> `H()`  [INFERRED]
  public/js/filament/forms/components/color-picker.js → public/js/filament/forms/components/markdown-editor.js
- `_freeze()` --indirect_call--> `t()`  [INFERRED]
  public/js/filament/forms/components/file-upload.js → public/js/filament/forms/components/date-time-picker.js
- `$s()` --indirect_call--> `r()`  [INFERRED]
  public/js/filament/forms/components/rich-editor.js → public/js/filament/forms/components/date-time-picker.js
- `vl()` --indirect_call--> `im()`  [INFERRED]
  public/js/filament/forms/components/file-upload.js → public/js/filament/forms/components/rich-editor.js
- `getExtension()` --indirect_call--> `Yt()`  [INFERRED]
  public/js/filament/forms/components/file-upload.js → public/js/filament/forms/components/markdown-editor.js

## Import Cycles
- None detected.

## Communities (176 total, 6 thin omitted)

### Community 0 - "code-editor.js"
Cohesion: 0.01
Nodes (95): Ac(), addCompletion(), addCompletions(), addNamespace(), addNamespaceObject(), Ag(), bd(), Bh() (+87 more)

### Community 1 - "chart.js"
Cohesion: 0.01
Nodes (108): abutsStart(), addControllers(), addPlugins(), addScales(), bd(), Be(), beforeDraw(), bm() (+100 more)

### Community 2 - "rich-editor.js"
Cohesion: 0.01
Nodes (152): [g](), $0(), Ab(), ac(), addHackNode(), addOptions(), addTextblockHacks(), af() (+144 more)

### Community 3 - "markdown-editor.js"
Cohesion: 0.03
Nodes (186): define(), Ei(), Aa(), Ac(), ad(), Ae(), af(), ai() (+178 more)

### Community 4 - "l"
Cohesion: 0.05
Nodes (140): attrs(), cc(), ensureLineGaps(), gapSize(), measureVisibleLineHeights(), On(), rc(), rn() (+132 more)

### Community 5 - "chart.js"
Cohesion: 0.02
Nodes (101): Eo(), acquireContext(), addControllers(), addEventListener(), addPlugins(), addScales(), afterDraw(), al() (+93 more)

### Community 6 - "constructor"
Cohesion: 0.02
Nodes (161): add(), addChunk(), addEventListener(), addInfoPane(), addInner(), addToSet(), addWindowListeners(), adjust() (+153 more)

### Community 7 - "resolve"
Cohesion: 0.06
Nodes (99): Image(), ad(), after(), An(), Ay(), before(), between(), Bg() (+91 more)

### Community 8 - "W"
Cohesion: 0.03
Nodes (118): aa(), acceptToken(), allows(), AQ(), au(), b1(), Bg(), child() (+110 more)

### Community 9 - "y"
Cohesion: 0.13
Nodes (65): Dg(), Ig(), le(), Se(), at(), Be(), ca(), cd() (+57 more)

### Community 10 - "fromObject"
Cohesion: 0.03
Nodes (112): ac(), ae(), after(), ag(), Al(), Am(), before(), bl() (+104 more)

### Community 11 - "get"
Cohesion: 0.03
Nodes (105): addBlockWidget(), addBreak(), addComposition(), addDelimiter(), addInlineWidget(), addLine(), addLineStart(), addLineStartIfNotCovered() (+97 more)

### Community 12 - "draw"
Cohesion: 0.05
Nodes (94): acquireContext(), adjustHitBoxes(), afterDraw(), bh(), bu(), buildTicks(), calculateLabelRotation(), _calculatePadding() (+86 more)

### Community 13 - ".slice"
Cohesion: 0.05
Nodes (102): addCommands(), addGlobalAttributes(), ak(), allowsMarks(), Ap(), apply(), applyInner(), applyTransaction() (+94 more)

### Community 14 - "updateElements"
Cohesion: 0.04
Nodes (86): addEventListener(), afterAutoSkip(), Ao(), applyStack(), aspectRatio(), au(), bindResponsiveEvents(), buildLookupTable() (+78 more)

### Community 15 - "_update"
Cohesion: 0.03
Nodes (100): $a(), active(), addBox(), addElements(), afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterFit() (+92 more)

### Community 16 - "create"
Cohesion: 0.04
Nodes (78): ar(), beforeDatasetDraw(), Cl(), clone(), create(), dc(), Dl(), dtFormatter() (+70 more)

### Community 17 - "lineAt"
Cohesion: 0.07
Nodes (44): addBlock(), addElement(), addLineDeco(), applyChanges(), balanced(), baseIndent(), baseIndentFor(), blankContent() (+36 more)

### Community 18 - "constructor"
Cohesion: 0.03
Nodes (119): add(), addExtensions(), addNode(), ag(), applyInitialSize(), Ar(), Bd(), Cc() (+111 more)

### Community 19 - "_update"
Cohesion: 0.05
Nodes (75): addElements(), ae(), afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterFit(), afterSetDimensions(), afterTickToLabelConversion() (+67 more)

### Community 20 - "id"
Cohesion: 0.06
Nodes (69): addNodeMark(), allowedMarks(), am(), bc(), canAppend(), canReplace(), clearIncompatible(), close() (+61 more)

### Community 21 - "facet"
Cohesion: 0.03
Nodes (110): a$(), accept(), activateHover(), active(), apply(), b0(), baseTheme(), between() (+102 more)

### Community 22 - "oi"
Cohesion: 0.06
Nodes (46): alpha(), co(), darken(), desaturate(), Gc(), greyscale(), gs(), $h() (+38 more)

### Community 23 - "tables.js"
Cohesion: 0.09
Nodes (62): A(), ae(), areRecordsSelected(), areRecordsToggleable(), B(), be(), C(), canSelectAllRecords() (+54 more)

### Community 24 - "r"
Cohesion: 0.06
Nodes (67): Af(), al(), AX(), B(), balance(), combine(), cw(), fromJSON() (+59 more)

### Community 25 - "E"
Cohesion: 0.05
Nodes (65): aa(), add(), B(), bo(), br(), bs(), ca(), _cachedScopes() (+57 more)

### Community 26 - "inRange"
Cohesion: 0.07
Nodes (41): average(), beforeDatasetsDraw(), beforeLayout(), bi(), cf(), dataset(), Do(), Dt() (+33 more)

### Community 27 - "constructor"
Cohesion: 0.07
Nodes (34): ad(), apply(), Bc(), bg(), chartOptionScopes(), constructor(), Cs(), dg() (+26 more)

### Community 28 - "advance"
Cohesion: 0.05
Nodes (61): addChild(), addGaps(), addLeafElement(), addNode(), advance(), ATXHeading(), blank(), break() (+53 more)

### Community 29 - "support.js"
Cohesion: 0.06
Nodes (40): as(), bo(), bs(), close(), closeQuietly(), co(), commit(), distribute() (+32 more)

### Community 30 - "Ke"
Cohesion: 0.10
Nodes (24): _a(), alpha(), color(), darken(), desaturate(), Ei(), fa(), Gi() (+16 more)

### Community 31 - ".append"
Cohesion: 0.07
Nodes (50): addMaps(), addStep(), addTransform(), append(), appendMap(), appendMapping(), appendMappingInverted(), as() (+42 more)

### Community 32 - "Filament\Schemas\Schema"
Cohesion: 0.06
Nodes (19): ListTaskReturSuppliers, TaskReturSupplierForm, UnitEnum, TaskReturSupplierResource, EditUser, ListUsers, UserForm, UnitEnum (+11 more)

### Community 33 - "eq"
Cohesion: 0.04
Nodes (64): activeForPoint(), addActive(), Ar(), as(), atLastNode(), be(), boundChange(), chunkEnd() (+56 more)

### Community 34 - "o"
Cohesion: 0.05
Nodes (67): yf(), o(), aa(), add(), ba(), br(), Bt(), ca() (+59 more)

### Community 35 - "Filament\Tables\Table"
Cohesion: 0.06
Nodes (14): ExpeditionsTable, MasterKendaraansTable, MasterSopirsTable, MasterTokosTable, SuppliersTable, TaskDatangMobilSuppliersTable, TaskKeluarBarangsTable, TaskKirimanMobilsTable (+6 more)

### Community 36 - "notifications.js"
Cohesion: 0.06
Nodes (31): actions(), button(), c(), close(), configureAnimations(), configureTransitions(), constructor(), danger() (+23 more)

### Community 37 - "reduce"
Cohesion: 0.05
Nodes (59): addActions(), advanceFully(), advanceStack(), allActions(), bi(), c0(), canShift(), close() (+51 more)

### Community 38 - "slice"
Cohesion: 0.05
Nodes (66): addChanges(), addSelection(), Ah(), applyTransaction(), asSingle(), compose(), continue(), create() (+58 more)

### Community 39 - "marks"
Cohesion: 0.06
Nodes (54): addAll(), addDOM(), addElement(), addElementByRule(), addStoredMark(), addTextNode(), addToSet(), allowsMarkType() (+46 more)

### Community 40 - "draw"
Cohesion: 0.06
Nodes (60): gu(), We(), adjustHitBoxes(), At(), beforeDatasetDraw(), beforeDatasetsDraw(), beforeDraw(), bi() (+52 more)

### Community 41 - "file-upload.js"
Cohesion: 0.06
Nodes (34): am(), Bp(), ca(), Cg(), clickPercent(), constructor(), Cp(), Dp() (+26 more)

### Community 42 - "updateElements"
Cohesion: 0.07
Nodes (47): Ao(), applyStack(), _calculateBarIndexPixels(), _calculateBarValuePixels(), calculateCircumference(), _circumference(), _computeGridLineItems(), countVisibleElements() (+39 more)

### Community 43 - "_notify"
Cohesion: 0.20
Nodes (14): active(), _animateOptions(), cancel(), _createAnimations(), _createDescriptors(), _descriptors(), _notify(), _notifyStateChanges() (+6 more)

### Community 44 - "sliceDoc"
Cohesion: 0.08
Nodes (33): aO(), charCategorizer(), compute(), cS(), Fc(), flatten(), focus(), from() (+25 more)

### Community 45 - "slider.js"
Cohesion: 0.09
Nodes (38): We(), Ae(), ar(), Be(), Bt(), De(), _e(), Ee() (+30 more)

### Community 46 - "find"
Cohesion: 0.12
Nodes (27): baseDirAt(), bidiIn(), bidiSpans(), bidiSpansAt(), checkHover(), coordsAtPos(), Df(), dirAt() (+19 more)

### Community 47 - "isHorizontal"
Cohesion: 0.07
Nodes (50): bh(), buildTicks(), calculateLabelRotation(), _calculatePadding(), cn(), _computeAngle(), _computeLabelItems(), _computeLabelSizes() (+42 more)

### Community 48 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.10
Nodes (15): StatsOverviewWidget, ArrivalSupplierTruck, Expedition, MasterKendaraan, MasterSopir, MasterToko, Supplier, TaskKeluarBarang (+7 more)

### Community 49 - "N"
Cohesion: 0.06
Nodes (46): afterAutoSkip(), Ar(), bc(), beforeLayout(), buildLookupTable(), cc(), dc(), determineDataLimits() (+38 more)

### Community 50 - "Xt"
Cohesion: 0.14
Nodes (40): At(), bi(), bn(), ci(), cn(), ct(), di(), dn() (+32 more)

### Community 51 - "ManageLeaves"
Cohesion: 0.09
Nodes (11): ManageLeaves, BackedEnum, UnitEnum, DivisionsTableWidget, WarehouseEmployeeImport, Division, WarehouseEmployee, WarehouseLeave (+3 more)

### Community 52 - "select.js"
Cohesion: 0.11
Nodes (29): Ae(), Ai(), An(), applyDisabledState(), Ce(), De(), disable(), ei() (+21 more)

### Community 53 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 54 - "Ue"
Cohesion: 0.08
Nodes (41): Rd(), Ax(), Ba(), bx(), Ch(), ct(), dx(), Eh() (+33 more)

### Community 55 - "select.js"
Cohesion: 0.12
Nodes (27): Ae(), An(), b(), Bt(), Ce(), De(), ei(), en() (+19 more)

### Community 56 - "Xt"
Cohesion: 0.16
Nodes (36): At(), bi(), ci(), cn(), ct(), di(), dn(), Dt() (+28 more)

### Community 57 - "echo.js"
Cohesion: 0.08
Nodes (18): a(), ar(), at(), cr(), d(), Dt(), f(), H() (+10 more)

### Community 58 - "Vn"
Cohesion: 0.17
Nodes (33): br(), Bt(), Ca(), ct(), Da(), Ea(), ei(), Fi() (+25 more)

### Community 59 - "Cn"
Cohesion: 0.15
Nodes (29): _a(), Ai(), ar(), c(), Cn(), destroy(), f(), g() (+21 more)

### Community 60 - "replace"
Cohesion: 0.08
Nodes (39): VariableDefinition(), _a(), cy(), deleteSelection(), dg(), E0(), focus(), forceFlush() (+31 more)

### Community 61 - "qt"
Cohesion: 0.16
Nodes (28): ae(), B(), de(), dt(), Ee(), fr(), Ge(), Gt() (+20 more)

### Community 62 - "wc"
Cohesion: 0.14
Nodes (17): ac(), cs(), Es(), Fo(), _getLegendItemAt(), ic(), interpolate(), lo() (+9 more)

### Community 63 - "getDatasetMeta"
Cohesion: 0.08
Nodes (34): themeClasses(), afterDatasetsUpdate(), buildOrUpdateControllers(), _destroyDatasetMeta(), generateLabels(), getController(), getDatasetMeta(), getDataVisibility() (+26 more)

### Community 64 - "SupplierResource"
Cohesion: 0.12
Nodes (6): ListSuppliers, SupplierForm, BackedEnum, UnitEnum, SupplierResource, SupplierImport

### Community 66 - "Y"
Cohesion: 0.09
Nodes (30): average(), dataset(), ec(), er(), Ge(), getCenterPoint(), getProps(), hasValue() (+22 more)

### Community 67 - "parse"
Cohesion: 0.07
Nodes (46): kx(), Ph(), af(), ah(), at(), Ba(), Bf(), contains() (+38 more)

### Community 68 - "fn"
Cohesion: 0.17
Nodes (24): aa(), Dn(), fn(), ha(), ia(), Ii(), Jr(), jt() (+16 more)

### Community 69 - "getDatasetMeta"
Cohesion: 0.08
Nodes (35): afterDatasetsUpdate(), An(), _d(), ef(), generateLabels(), getDatasetMeta(), getDataVisibility(), getIndexAngle() (+27 more)

### Community 70 - "parse"
Cohesion: 0.11
Nodes (30): buildOrUpdateScales(), ch(), D(), Ea(), endOf(), ensureScalesHaveIDs(), Fn(), format() (+22 more)

### Community 71 - "app.js"
Cohesion: 0.13
Nodes (11): close(), E(), G(), init(), P(), Q(), setUpResizeObserver(), X() (+3 more)

### Community 72 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.13
Nodes (6): ListTaskDatangMobilSuppliers, TaskDatangMobilSupplierForm, BackedEnum, UnitEnum, TaskDatangMobilSupplierResource, Illuminate\Database\Eloquent\Builder

### Community 73 - "TaskKeluarBarangResource.php"
Cohesion: 0.13
Nodes (6): CreateTaskKeluarBarang, ListTaskKeluarBarangs, TaskKeluarBarangForm, BackedEnum, UnitEnum, TaskKeluarBarangResource

### Community 74 - "TaskTerimaSupplierResource.php"
Cohesion: 0.13
Nodes (6): CreateTaskTerimaSupplier, ListTaskTerimaSuppliers, TaskTerimaSupplierForm, BackedEnum, UnitEnum, TaskTerimaSupplierResource

### Community 75 - "TaskReturCabangResource.php"
Cohesion: 0.16
Nodes (5): ListTaskReturCabangs, TaskReturCabangForm, BackedEnum, UnitEnum, TaskReturCabangResource

### Community 76 - "TaskIdGenerator"
Cohesion: 0.17
Nodes (6): RecentActivityWidget, ActivityLog, TaskIdGenerator, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 77 - "User.php"
Cohesion: 0.15
Nodes (10): User, ActivityLogSeeder, DatabaseSeeder, RoleSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User (+2 more)

### Community 79 - "toString"
Cohesion: 0.09
Nodes (29): accepts(), Bi(), Bw(), check(), checkAttrs(), endIndex(), getObj(), go() (+21 more)

### Community 81 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 82 - "render"
Cohesion: 0.23
Nodes (17): applyDisabledState(), closeDropdown(), constructor(), destroy(), disable(), enable(), focusNextOption(), focusPreviousOption() (+9 more)

### Community 83 - "ExpeditionResource"
Cohesion: 0.16
Nodes (5): ExpeditionResource, BackedEnum, UnitEnum, ListExpeditions, ExpeditionForm

### Community 84 - "MasterKendaraanResource"
Cohesion: 0.16
Nodes (5): MasterKendaraanResource, BackedEnum, UnitEnum, ListMasterKendaraans, MasterKendaraanForm

### Community 85 - "MasterSopirResource"
Cohesion: 0.16
Nodes (5): MasterSopirResource, BackedEnum, UnitEnum, ListMasterSopirs, MasterSopirForm

### Community 86 - "MasterTokoResource"
Cohesion: 0.16
Nodes (5): MasterTokoResource, BackedEnum, UnitEnum, ListMasterTokos, MasterTokoForm

### Community 87 - "e"
Cohesion: 0.16
Nodes (16): Jt(), A(), be(), bn(), e(), gt(), In(), jn() (+8 more)

### Community 88 - "Filament\Resources\Pages\CreateRecord"
Cohesion: 0.20
Nodes (5): CreateTaskDatangMobilSupplier, CreateTaskReturCabang, CreateTaskReturSupplier, CreateUser, Filament\Resources\Pages\CreateRecord

### Community 89 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 90 - "color-picker.js"
Cohesion: 0.15
Nodes (3): style(), update(), [x]()

### Community 91 - "En"
Cohesion: 0.19
Nodes (14): apply(), At(), cs(), En(), Hr(), it(), Mt(), _o() (+6 more)

### Community 92 - "render"
Cohesion: 0.30
Nodes (14): closeDropdown(), constructor(), destroy(), focusNextOption(), focusPreviousOption(), getVisibleOptions(), handleDropdownKeydown(), handleSelectButtonKeydown() (+6 more)

### Community 93 - "Login.php"
Cohesion: 0.22
Nodes (6): Login, AdminPanelProvider, Filament\Auth\Pages\Login, Filament\Panel, Filament\PanelProvider, Filament\Schemas\Components\Component

### Community 94 - "t"
Cohesion: 0.17
Nodes (13): Ce(), De(), Ht(), Ie(), ii(), Me(), oi(), Re() (+5 more)

### Community 95 - "te"
Cohesion: 0.05
Nodes (12): Ud(), Bi(), Bn(), br(), fe(), Id(), ji(), on() (+4 more)

### Community 96 - "selectOption"
Cohesion: 0.28
Nodes (13): addBadgesForSelectedOptions(), addSingleBadge(), addSingleSelectionDisplay(), createBadgeElement(), createRemoveButton(), getLabelForSingleSelection(), getLabelsForMultipleSelection(), getSelectedOptionLabel() (+5 more)

### Community 97 - "renderOptions"
Cohesion: 0.37
Nodes (13): createOptionElement(), deferPositionDropdown(), filterOptions(), handleSearch(), hideLoadingState(), openDropdown(), populateLabelRepositoryFromOptions(), positionDropdown() (+5 more)

### Community 98 - "selectOption"
Cohesion: 0.28
Nodes (13): addBadgesForSelectedOptions(), addSingleBadge(), addSingleSelectionDisplay(), createBadgeElement(), createRemoveButton(), getLabelForSingleSelection(), getLabelsForMultipleSelection(), getSelectedOptionLabel() (+5 more)

### Community 99 - "renderOptions"
Cohesion: 0.37
Nodes (13): createOptionElement(), deferPositionDropdown(), filterOptions(), handleSearch(), hideLoadingState(), openDropdown(), populateLabelRepositoryFromOptions(), positionDropdown() (+5 more)

### Community 100 - "Symfony\Component\HttpFoundation\Response"
Cohesion: 0.24
Nodes (4): EmployeesExport, SuppliersExport, SupplierTemplateExport, Symfony\Component\HttpFoundation\Response

### Community 101 - "TaskKirimanMobilResource.php"
Cohesion: 0.14
Nodes (6): CreateTaskKirimanMobil, ListTaskKirimanMobils, TaskKirimanMobilForm, BackedEnum, UnitEnum, TaskKirimanMobilResource

### Community 102 - "composer.json"
Cohesion: 0.17
Nodes (11): description, keywords, license, minimum-stability, name, prefer-stable, repositories, $schema (+3 more)

### Community 103 - "r"
Cohesion: 0.22
Nodes (11): Be(), ei(), Fe(), He(), le(), Mt(), ni(), r() (+3 more)

### Community 104 - "_e"
Cohesion: 0.22
Nodes (11): ba(), c(), _e(), It(), jt(), ot(), s(), sa() (+3 more)

### Community 105 - "oe"
Cohesion: 0.29
Nodes (11): cm(), De(), dm(), Ht(), me(), nm(), oe(), q() (+3 more)

### Community 106 - "addInner"
Cohesion: 0.17
Nodes (16): addInner(), bl(), bu(), dy(), findIndex(), locals(), localsInner(), Lr() (+8 more)

### Community 107 - "Rn"
Cohesion: 0.22
Nodes (10): b(), Bt(), Ln(), _n(), Ot(), Rn(), ve(), Ye() (+2 more)

### Community 108 - "actions.js"
Cohesion: 0.53
Nodes (8): closeModal(), generateModalId(), getActionNestingIndexFromModalId(), init(), openModal(), rememberPreviouslyFocusedElement(), restorePreviouslyFocusedElement(), syncActionModals()

### Community 109 - "e"
Cohesion: 0.28
Nodes (9): be(), e(), gt(), In(), jn(), Ln(), O(), S() (+1 more)

### Community 111 - "require-dev"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 112 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 114 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 115 - "require"
Cohesion: 0.29
Nodes (7): require, andreia/filament-nord-theme, filament/filament, laravel/framework, laravel/tinker, php, spatie/laravel-permission

### Community 116 - "TestCase"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 117 - "e"
Cohesion: 0.33
Nodes (5): di(), e(), g(), i(), xr()

### Community 120 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 121 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 123 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **61 isolated node(s):** `Controller`, `$schema`, `name`, `repositories`, `type` (+56 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **6 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `o()` connect `o` to `chart.js`, `rich-editor.js`, `markdown-editor.js`, `l`, `chart.js`, `constructor`, `resolve`, `W`, `fromObject`, `get`, `draw`, `.slice`, `updateElements`, `_update`, `create`, `constructor`, `_update`, `id`, `facet`, `r`, `E`, `inRange`, `constructor`, `advance`, `support.js`, `Ke`, `.append`, `eq`, `reduce`, `slice`, `marks`, `draw`, `file-upload.js`, `_notify`, `sliceDoc`, `isHorizontal`, `N`, `Xt`, `select.js`, `Ue`, `select.js`, `Xt`, `echo.js`, `Cn`, `replace`, `wc`, `getDatasetMeta`, `Y`, `parse`, `fn`, `getDatasetMeta`, `app.js`, `toString`, `render`, `e`, `render`, `t`, `selectOption`, `renderOptions`, `selectOption`, `renderOptions`, `r`, `_e`, `oe`, `addInner`, `Rn`, `e`?**
  _High betweenness centrality (0.112) - this node is a cross-community bridge._
- **Why does `l()` connect `l` to `code-editor.js`, `chart.js`, `rich-editor.js`, `markdown-editor.js`, `chart.js`, `constructor`, `resolve`, `W`, `y`, `fromObject`, `get`, `draw`, `.slice`, `updateElements`, `_update`, `create`, `lineAt`, `constructor`, `id`, `facet`, `tables.js`, `r`, `E`, `inRange`, `constructor`, `advance`, `.append`, `eq`, `o`, `reduce`, `slice`, `marks`, `draw`, `sliceDoc`, `isHorizontal`, `Xt`, `Ue`, `Xt`, `echo.js`, `replace`, `qt`, `getDatasetMeta`, `Y`, `parse`, `fn`, `getDatasetMeta`, `parse`, `toString`, `e`, `_e`, `oe`, `Rn`, `e`?**
  _High betweenness centrality (0.083) - this node is a cross-community bridge._
- **Why does `u()` connect `l` to `markdown-editor.js`, `constructor`, `resolve`, `W`, `fromObject`, `get`, `draw`, `.slice`, `updateElements`, `lineAt`, `constructor`, `_update`, `id`, `facet`, `tables.js`, `r`, `E`, `inRange`, `constructor`, `support.js`, `.append`, `eq`, `o`, `reduce`, `draw`, `updateElements`, `sliceDoc`, `slider.js`, `isHorizontal`, `Xt`, `select.js`, `select.js`, `Xt`, `Vn`, `Cn`, `replace`, `qt`, `wc`, `Y`, `parse`, `fn`, `parse`, `app.js`, `e`, `Rn`, `e`, `e`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Are the 232 inferred relationships involving `o()` (e.g. with `G()` and `Be()`) actually correct?**
  _`o()` has 232 INFERRED edges - model-reasoned connections that need verification._
- **Are the 204 inferred relationships involving `l()` (e.g. with `L()` and `advance()`) actually correct?**
  _`l()` has 204 INFERRED edges - model-reasoned connections that need verification._
- **Are the 183 inferred relationships involving `r()` (e.g. with `Af()` and `al()`) actually correct?**
  _`r()` has 183 INFERRED edges - model-reasoned connections that need verification._
- **Are the 163 inferred relationships involving `t()` (e.g. with `add()` and `addCompletions()`) actually correct?**
  _`t()` has 163 INFERRED edges - model-reasoned connections that need verification._