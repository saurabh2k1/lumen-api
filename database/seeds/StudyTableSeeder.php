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
            '_id' => Uuid::generate(4),
            'name' => 'Vivinex Study',
            'code' => 'VIOL-IND-001-PMCF',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        if (app()->environment('local')) {
            DB::table('sites')->delete();
        }

        Site::create([
            'id' => 1,
            '_id' => Uuid::generate(4),

            'name' => 'Aravind Eye Hospital',
            'code' => '001',
            'address' => '1, Anna Nagar, Madhuri-625020
            Tamil Nadu, India',
            'contact_person' => 'Dr. Madhu Shekhar',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Site::create([
            'id' => 2,
            '_id' => Uuid::generate(4),

            'name' => 'LV Prasad Eye Institute',
            'code' => '002',
            'address' => 'Kallam Anji Reddy Campus, 
            LV Prasad Marg, Banjara Hills, 
            Hyderabad-500 034
            Telangana, India',
            'contact_person' => 'Dr. Jagadesh C. Reddy',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Site::create([
            'id' => 3,
            '_id' => Uuid::generate(4),

            'name' => 'Laxmi Eye Institute',
            'code' => '003',
            'address' => 'Uran Road, Panvel, 
            Navi Mumbai-40206, Maharashtra, India',
            'contact_person' => 'Dr. Suhas Haldipurkar',
            'created_by' => 1,
            'updated_by' => 1,
        ]);


        if (app()->environment('local')) {
            DB::table('site_study')->delete();
        }

        SiteStudy::create([
            'site_id' => 1,
            'study_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        SiteStudy::create([
            'site_id' => 2,
            'study_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        SiteStudy::create([
            'site_id' => 3,
            'study_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

    }
}
