<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->query('title');

        if ($name) {
            $courses = Course::where('title', 'like', '%' . $name . '%')->with("category")->get();
        } else {
            $courses = Course::with("category")->get();
        }

        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::with([
            'category:id,title',
            'course_modules.lessons:id,course_module_id,title'
        ])->findOrFail($id);

        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function buy($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        if ($user->courses()->where('course_id', $id)->exists()) {
            return response()->json([
                'message' => 'У вас уже есть этот курс.'
            ], 400);
        }

        if ($user->balance < $course->price) {
            return response()->json([
                'message' => 'Недостаточно средств.'
            ], 400);
        }

        $user->balance -= $course->price;
        $user->save();

        UserCourse::create([
           "user_id" => $user->id,
           "course_id" => $id,
        ]);

        return response()->json([
            'message' => 'Курс успешно куплен.',
            'course' => $course,
            'remaining_balance' => $user->balance
        ], 200);
    }

    public function certificate($id, Request $request)
    {
        $user = Auth::user();
        $course = $user->courses()->where('course_id', $id)->first();

        if (!$course) {
            return response()->json([
                'message' => 'У вас нет этого курса',
            ], 403);
        }

        // Send the request to Bannerbear API to initiate certificate generation
        $response = Http::withToken(env('BANNERBEAR_API_KEY'))->post('https://api.bannerbear.com/v2/images', [
            'template' => '1oMJnB5rPegPbl2wqL',
            'modifications' => [
                [
                    'name' => 'user name',
                    'text' => $request->full_name,
                ],
                [
                    'name' => 'course info',
                    'text' => "Has completed the \"{$course->title}\" at Jet Academy",
                ],
                [
                    'name' => 'serialnumber',
                    'text' => 'JET-SC-' . random_int(10, 99),
                ],
            ],
            'webhook_url' => null,
            'transparent' => false,
            'metadata' => null,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Failed to generate certificate.',
                'error' => $response->json(), // Include the error for debugging
            ], 500);
        }

        // Extract the 'self' URL for polling
        $selfUrl = $response->json()['self'];

        // Poll the status until the certificate is ready
        for ($i = 0; $i < 10; $i++) { // Poll up to 10 times with delays
            sleep(5); // Wait 5 seconds between each poll

            $statusResponse = Http::withToken(env('BANNERBEAR_API_KEY'))->get($selfUrl);

            if (!$statusResponse->successful()) {
                return response()->json([
                    'message' => 'Failed to fetch certificate status.',
                    'error' => $statusResponse->json(),
                ], 500);
            }

            $statusData = $statusResponse->json();

            // Check if the certificate is ready (image or PDF URL available)
            if (!empty($statusData['image_url_png'])) {
                // Download the PNG certificate
                $imageContent = Http::get($statusData['image_url_png'])->body();

                // Define the filename for the download
                $fileName = "certificate_{$user->name}_{$course->title}.png";

                // Serve the image as a downloadable file
                return response($imageContent)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', "attachment; filename={$fileName}");
            } elseif (!empty($statusData['pdf_url'])) {
                // Download the PDF certificate if available
                $pdfContent = Http::get($statusData['pdf_url'])->body();

                $fileName = "certificate_{$user->name}_{$course->title}.pdf";

                return response($pdfContent)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', "attachment; filename={$fileName}");
            }
        }

        // If the certificate is still not ready after polling
        return response()->json([
            'message' => 'Certificate is still being generated. Please try again later.',
        ], 202);
    }

}
