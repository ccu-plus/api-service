<?php

declare(strict_types=1);

namespace App\Commands\Course;

use App\Importer\Importer;
use App\Models\Course;
use App\Models\Department;
use App\Models\Dimension;
use App\Models\Professor;
use App\Models\Semester;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:import {semester : 學期} {--force : 強制執行} {--dry-run : 顯示新增課程而不實際新增}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匯入指定學期課程資料至資料庫';

    /**
     * Course data importer.
     *
     * @var Importer
     */
    protected $importer;

    /**
     * Import constructor.
     */
    public function __construct(Importer $importer)
    {
        parent::__construct();

        Course::disableSearchSyncing();

        $this->importer = $importer;
    }

    /**
     * Import destructor.
     */
    public function __destruct()
    {
        Course::enableSearchSyncing();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (is_null($semester = $this->semester())) {
            return;
        }

        $data = $this->importer->get($semester->value);

        $departments = $this->departments($data);

        $dimensions = Dimension::all()->pluck('id', 'name')->toArray();

        $professors = $this->professors($data);

        foreach ($data as $datum) {
            foreach ($datum['courses'] as $info) {
                if ($this->option('dry-run')) {
                    $this->info(sprintf('新增 %s（%s） 課程', $info['name']['cht'], $info['code']));

                    continue;
                }

                /** @var Course $course */
                $course = Course::query()->firstOrCreate(['code' => $info['code']], [
                    'name' => $info['name']['cht'],
                    'name_en' => $info['name']['eng'],
                    'name_pinyin' => to_ascii($info['name']['cht']),
                    'credit' => $info['credit'],
                    'department_id' => $departments[$datum['code']],
                    'dimension_id' => $dimensions[$info['dimension'] ?? null] ?? null,
                ]);

                $course->semesters()->syncWithoutDetaching($semester);

                foreach ($info['professor'] as $professor) {
                    $attributes = [
                        'course_id' => $course->getKey(),
                        'professor_id' => $professors[$professor],
                        'semester_id' => $semester->getKey(),
                    ];

                    $pivot = $course->professors()->newPivot();

                    if (! $pivot->where($attributes)->exists()) {
                        $pivot->insert($attributes);
                    }
                }
            }
        }
    }

    /**
     * 取得學期 Eloquent Model.
     */
    protected function semester(): ?Semester
    {
        $name = sprintf(
            '%s%s',
            substr($this->argument('semester'), 0, 3),
            Str::endsWith($this->argument('semester'), '1') ? '上' : '下'
        );

        /** @var Semester $semester */
        $semester = Semester::query()->firstOrCreate(['name' => $name]);

        if ($semester->wasRecentlyCreated || $this->option('force')) {
            return $semester;
        }

        $this->error(sprintf('%s 學期課程資料已匯入，如仍欲執行，請加上 --force', $semester->name));

        return null;
    }

    /**
     * 確保系所存在，並取得所有系所資料.
     */
    protected function departments(array $departments): array
    {
        $exists = Department::all();

        foreach ($departments as ['college' => $college, 'name' => $name, 'code' => $code]) {
            $cdept = $exists->firstWhere('code', '=', $code);

            $ndept = $exists->where('college', '=', $college)->firstWhere('name', '=', $name);

            if (is_null($cdept) && is_null($ndept)) { // 如果皆為 null，代表尚無此系所資料
                Department::query()->create(compact('college', 'name', 'code'));
            } elseif (! is_null($cdept) && is_null($ndept)) { // 如果 code 存在但名稱不存在，代表系所名稱變更
                $cdept->update(compact('college', 'name'));
            } elseif (is_null($cdept) && ! is_null($ndept)) { // 如果名稱存在但 code 不存在，代表系所代碼變更
                $ndept->update(compact('code'));
            }
        }

        return Department::all()->pluck('id', 'code')->toArray();
    }

    /**
     * 確保教授存在，並取得所有教授資料.
     */
    protected function professors(array $departments): array
    {
        collect($departments)
            ->pluck('courses')
            ->collapse()
            ->pluck('professor')
            ->flatten()
            ->unique()
            ->values()
            ->map(function (string $name): string {
                $map = [
                    '李?玲' => '李䊵玲', // BIG-5 無「䊵」此字
                ];

                return $map[$name] ?? $name;
            })
            ->diff(Professor::all()->pluck('name')->toArray())
            ->chunk(50)
            ->each(function (Collection $names): void {
                Professor::query()->insert(array_map(function (string $name): array {
                    return ['name' => $name];
                }, $names->values()->toArray()));
            });

        return Professor::all()->pluck('id', 'name')->toArray();
    }
}
