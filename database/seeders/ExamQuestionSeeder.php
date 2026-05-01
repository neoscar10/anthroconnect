<?php

namespace Database\Seeders;

use App\Models\Exam\ExamQuestion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@anthroconnect.com')->first() ?? User::first();
        
        $questions = [
            [
                'title' => 'Cultural Relativism vs Ethnocentrism',
                'question_text' => 'Critically examine the concept of Cultural Relativism. How has it helped in countering ethnocentrism in anthropological research?',
                'exam_type' => 'UPSC',
                'year' => '2023',
                'marks' => 15,
                'word_limit' => 250,
                'status' => 'published',
                'published_at' => now(),
                'question_kind' => 'model',
                'answer_guidelines' => "### Introduction\nDefine Cultural Relativism as proposed by Franz Boas.\n\n### Body\n1. Explain the historical context against racial determinism.\n2. Discuss the methodological value in fieldwork.\n3. Mention the criticism (Moral vs Methodological Relativism).\n\n### Conclusion\nSummarize its importance in modern globalized anthropology.",
                'evaluation_rubric' => [
                    ['criteria' => 'Definition of Cultural Relativism', 'marks' => '3'],
                    ['criteria' => 'Linkage with Ethnocentrism', 'marks' => '5'],
                    ['criteria' => 'Critical Analysis/Critique', 'marks' => '4'],
                    ['criteria' => 'Conclusion & Clarity', 'marks' => '3'],
                ],
                'learning_resources' => [
                    ['title' => 'Franz Boas & Historical Particularism', 'type' => 'Encyclopedia', 'url' => '/encyclopedia/concepts/historical-particularism'],
                    ['title' => 'Ethnocentrism Explained', 'type' => 'Video', 'url' => 'https://example.com/ethnocentrism'],
                ],
            ],
            [
                'title' => 'Lineage and Clan',
                'question_text' => 'Distinguish between lineage and clan with suitable ethnographic examples. Discuss their role in traditional tribal political organizations.',
                'exam_type' => 'UPSC',
                'year' => '2022',
                'marks' => 20,
                'word_limit' => 250,
                'status' => 'published',
                'published_at' => now(),
                'question_kind' => 'model',
                'answer_guidelines' => "### Core Points\n- Definitions (E.E. Evans-Pritchard, Meyer Fortes).\n- Segmentation and corporate nature.\n- Totemic links for clans.\n- Segmentary lineage systems (Nuer/Tiv).",
                'model_answer' => "Lineage is a unilineal descent group that can trace its ancestry back to a known common ancestor. In contrast, a clan is a descent group where members claim common ancestry but cannot trace the specific links...\n\nIn political organizations, especially segmentary ones, lineages provide the structural framework for mobilization during conflict (Mass complementation)...",
                'evaluation_rubric' => [
                    ['criteria' => 'Clarity in distinction', 'marks' => '5'],
                    ['criteria' => 'Ethnographic examples (Nuer, Tallensi, etc)', 'marks' => '5'],
                    ['criteria' => 'Political role analysis', 'marks' => '7'],
                    ['criteria' => 'Structure & Conclusion', 'marks' => '3'],
                ],
            ],
            [
                'title' => 'Fieldwork Tradition in Anthropology',
                'question_text' => 'Write a short note on the development of fieldwork tradition in anthropology, from "Armchair Anthropology" to "Participant Observation".',
                'exam_type' => 'UPSC',
                'year' => '2024',
                'marks' => 10,
                'word_limit' => 150,
                'status' => 'published',
                'published_at' => now(),
                'question_kind' => 'past',
                'answer_guidelines' => "### Stages\n1. Armchair phase (Tylor, Frazer).\n2. Transitional phase (Boas, Haddon - Torres Straits).\n3. Malinowskian revolution (Argonauts of the Western Pacific).\n4. Modern developments.",
            ]
        ];

        foreach ($questions as $q) {
            $q['slug'] = Str::slug($q['title']);
            $q['created_by'] = $admin->id;
            $q['updated_by'] = $admin->id;
            
            $examQuestion = ExamQuestion::create($q);
            
            // Random tags if any exist
            $tags = Tag::inRandomOrder()->limit(2)->pluck('id');
            if ($tags->isNotEmpty()) {
                $examQuestion->syncTags($tags->toArray());
            }
        }
    }
}
