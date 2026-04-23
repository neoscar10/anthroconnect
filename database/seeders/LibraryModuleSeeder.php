<?php

namespace Database\Seeders;

use App\Models\LibraryResource;
use App\Models\LibraryResourceType;
use App\Models\LibraryRegion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LibraryModuleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        if (!$admin) return;

        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LibraryResource::truncate();
        LibraryResourceType::truncate();

        LibraryRegion::truncate();
        DB::table('library_resource_topic')->truncate();
        DB::table('library_resource_tag')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Resource Types
        $types = [
            ['name' => 'Journal Article', 'description' => 'Peer-reviewed scholarly articles.'],
            ['name' => 'Book', 'description' => 'Monographs and edited volumes.'],
            ['name' => 'Conference Paper', 'description' => 'Papers presented at academic conferences.'],
            ['name' => 'Thesis / Dissertation', 'description' => 'Doctoral or Masters level research.'],
            ['name' => 'Research Report', 'description' => 'Field reports and institutional findings.'],
        ];

        $typeModels = [];
        foreach ($types as $type) {
            $typeModels[] = LibraryResourceType::create([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'is_active' => true,
            ]);
        }



        // 3. Regions
        $regions = [
            ['name' => 'South Asia', 'description' => 'Including India, Pakistan, Bangladesh, Nepal, Sri Lanka.'],
            ['name' => 'Sub-Saharan Africa', 'description' => 'Central, East, Southern, and West Africa.'],
            ['name' => 'Southeast Asia', 'description' => 'Maritime and Mainland Southeast Asia.'],
            ['name' => 'Oceania', 'description' => 'Australia, New Zealand, Melanesia, Micronesia, Polynesia.'],
            ['name' => 'Latin America', 'description' => 'Central and South America, Caribbean.'],
        ];

        $regModels = [];
        foreach ($regions as $reg) {
            $regModels[] = LibraryRegion::create([
                'name' => $reg['name'],
                'slug' => Str::slug($reg['name']),
                'description' => $reg['description'],
                'is_active' => true,
            ]);
        }

        // 4. Resources
        $resourcesData = [
            [
                'title' => 'Structural Analysis in Linguistics and in Anthropology',
                'author_display' => 'Claude Lévi-Strauss',
                'publication_year' => 1945,
                'publisher' => 'Word',
                'type' => 'Journal Article',
                'region' => 'South Asia',
                'abstract' => 'This seminal paper explores the application of structural linguistics to the study of kinship and social organization, laying the groundwork for structuralism in anthropology.',
            ],
            [
                'title' => 'The Nuer: A Description of the Modes of Livelihood and Political Institutions of a Nilotic People',
                'author_display' => 'E. E. Evans-Pritchard',
                'publication_year' => 1940,
                'publisher' => 'Clarendon Press',
                'type' => 'Book',
                'region' => 'Sub-Saharan Africa',
                'abstract' => 'A classic ethnography of the Nuer people of South Sudan, focusing on their pastoral economy and segmentary lineage system.',
            ],
            [
                'title' => 'Purity and Danger: An Analysis of Concepts of Pollution and Taboo',
                'author_display' => 'Mary Douglas',
                'publication_year' => 1966,
                'publisher' => 'Routledge',
                'type' => 'Book',
                'region' => 'South Asia',
                'abstract' => 'An exploration of how social groups use concepts of dirt and pollution to maintain social boundaries and symbolic order.',
            ],
            [
                'title' => 'Deep Play: Notes on the Balinese Cockfight',
                'author_display' => 'Clifford Geertz',
                'publication_year' => 1972,
                'publisher' => 'Daedalus',
                'type' => 'Journal Article',
                'region' => 'Southeast Asia',
                'abstract' => 'A foundational text for interpretive anthropology, analyzing the Balinese cockfight as a "cultural text" that expresses deep-seated social anxieties.',
            ],
            [
                'title' => 'The Gift: Forms and Functions of Exchange in Archaic Societies',
                'author_display' => 'Marcel Mauss',
                'publication_year' => 1925,
                'publisher' => 'L\'Année Sociologique',
                'type' => 'Book',
                'region' => 'Oceania',
                'abstract' => 'A comparative study of gift-giving and exchange, arguing that reciprocity is a fundamental basis of social solidarity.',
            ],
        ];

        $topicIds = Topic::pluck('id')->toArray();

        foreach ($resourcesData as $data) {
            $typeModel = collect($typeModels)->firstWhere('name', $data['type']);
            $regModel = collect($regModels)->firstWhere('name', $data['region']);

            $resource = LibraryResource::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'author_display' => $data['author_display'],
                'publication_year' => $data['publication_year'],
                'publisher' => $data['publisher'],
                'abstract' => $data['abstract'],
                'resource_type_id' => $typeModel->id,
                'region_id' => $regModel->id,
                'status' => 'published',
                'published_at' => now(),
                'access_type' => 'public',
                'created_by' => $admin->id,
            ]);

            if (!empty($topicIds)) {
                $resource->topics()->attach(array_rand(array_flip($topicIds), rand(1, 2)));
            }
        }
    }
}
