<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Call the Gemini API to generate an AI summary and determine priority.
     *
     * @param Task $task
     * @return array An array containing 'ai_summary' and 'ai_priority'
     * @throws \Exception If the API key is missing or the response is invalid
     */
    public function generateSummary(Task $task): array
    {
        $prompt = "Given this task:\nTitle: {$task->title}\nDescription: {$task->description}\nDue: {$task->due_date}\n\nReturn a JSON object with keys:\n- 'ai_summary' (2-3 sentence summary of the task)\n- 'ai_priority' (one of: low, medium, high based on urgency and complexity).\n\nOnly return valid JSON without any markdown formatting.";

        try {
            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception('Gemini API key is not set');
            }

            $client = \Gemini::client($apiKey);
            $response = $client->generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);
            
            $text = $response->text();
            
            // Clean markdown if the AI includes it
            $text = str_replace(['```json', '```'], '', $text);
            
            $data = json_decode(trim($text), true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['ai_summary'], $data['ai_priority'])) {
                return $data;
            }

            throw new \Exception('Invalid JSON from Gemini');
        } catch (\Exception $e) {
            Log::error('Gemini AI Summary Failed: ' . $e->getMessage());

            return [
                'ai_summary' => 'Failed to generate AI summary. Task involves: ' . $task->title,
                'ai_priority' => 'medium',
            ];
        }
    }
}
