<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;

class QrController extends Controller
{
    public function showAnimalByQr(string $token): JsonResponse
    {
        $animal = Animal::query()->where('qr_token', $token)->with('participants')->firstOrFail();

        return response()->json($animal);
    }

    public function showVolunteerByQr(string $token): JsonResponse
    {
        $volunteer = Volunteer::query()->where('qr_token', $token)->with('availabilities')->firstOrFail();

        return response()->json($volunteer);
    }
}
