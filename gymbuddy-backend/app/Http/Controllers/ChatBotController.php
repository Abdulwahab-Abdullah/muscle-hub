<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // حفظ رسالة المستخدم
        $userMessage = AiChatHistory::create([
            'user_id' => $userId,
            'message' => $validated['message'],
            'sender' => 'user',
        ]);

        try {
            // جلب آخر 9 رسائل من التاريخ
            $conversationHistory = AiChatHistory::where('user_id', $userId)
                ->latest()
                ->take(9)
                ->get()
                ->sortBy('created_at');

            // بناء الـ prompt مع التاريخ
            $systemPrompt = "You are a helpful AI fitness coach for a gym app called MuscleHub. Your role is to:\n";
            $systemPrompt .= "- Provide expert advice on fitness, nutrition, and workouts\n";
            $systemPrompt .= "- Keep responses concise, practical, and motivating\n";
            $systemPrompt .= "- Use simple language and avoid medical advice\n";
            $systemPrompt .= "- Focus on safe, evidence-based recommendations\n\n";

            $conversationText = $systemPrompt;

            foreach ($conversationHistory as $msg) {
                $role = $msg->sender === 'user' ? 'User' : 'Assistant';
                $conversationText .= "{$role}: {$msg->message}\n\n";
            }

            $conversationText .= "User: {$validated['message']}\n\nAssistant:";

            // استدعاء Gemini API
            $apiKey = env('GEMINI_API_KEY');

            if (empty($apiKey)) {
                throw new \Exception('Gemini API key is not configured');
            }

            // ✅ النموذج الصحيح
            $model = 'gemini-2.5-flash'; // يمكنك تغييره إلى gemini-2.5-pro أو gemini-pro-latest

            $response = Http::timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $conversationText]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                        'topP' => 0.9,
                        'topK' => 40,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error: ' . $response->body());
                throw new \Exception('AI service is temporarily unavailable. Please try again later.');
            }

            $data = $response->json();

            $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Sorry, I couldn't generate a response.";

            // حفظ رد البوت
            $botMessage = AiChatHistory::create([
                'user_id' => $userId,
                'message' => trim($botReply),
                'sender' => 'bot',
            ]);

            return response()->json([
                'success' => true,
                'user_message' => $userMessage,
                'bot_reply' => $botMessage
            ]);
        } catch (\Throwable $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Unable to process your request',
                'message' => config('app.debug') ? $e->getMessage() : 'AI service is temporarily unavailable. Please try again later.'
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) return response()->json(['message' => 'Unauthorized'], 401);

        if (Auth::user()->role === 'admin' && $request->has('user_id')) {
            $validated = $request->validate(['user_id' => 'required|exists:users,id']);
            $userId = $validated['user_id'];
        }

        $history = AiChatHistory::where('user_id', $userId)->orderBy('created_at', 'asc')->get();

        return response()->json(['success' => true, 'history' => $history]);
    }

    public function clearHistory(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) return response()->json(['message' => 'Unauthorized'], 401);

        if (Auth::user()->role === 'admin' && $request->has('user_id')) {
            $validated = $request->validate(['user_id' => 'required|exists:users,id']);
            $userId = $validated['user_id'];
        }

        $deletedCount = AiChatHistory::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared successfully',
            'deleted_count' => $deletedCount
        ]);
    }
}
