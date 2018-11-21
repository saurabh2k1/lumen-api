<?php
use App\Study;
use App\SiteStudy;
use App\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class StudyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        if (app()->environment('local')) {
            DB::table('studies')->delete();
        }

        Study::create([
            'id' => 1,
            'name' => 'Vivinex Study',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        if (app()->environment('local')) {
            DB::table('sites')->delete();
        }

        Site::create([
            'id' => 1,
            '_id' => Uuid::generate(4),

            'name' => 'Site 1',
            'code' => 'VS1',
            'address' => 'Shahdara, Delhi - 110032',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        if (app()->environment('local')) {
            DB::table('user_role')->delete();
        }

        SiteStudy::create([
            'site_id' => 1,
            'study_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

    }
}
