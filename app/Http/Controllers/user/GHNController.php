<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GHNService;
use App\Services\VietnamLocationData;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    public function getProvinces(GHNService $ghn)
    {
        $res = $ghn->getProvinces();
        if (isset($res['code']) && $res['code'] === 200 && !empty($res['data'])) {
            return response()->json($res);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success (Local Fallback)',
            'data' => VietnamLocationData::getProvinces(),
        ]);
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        $res = $ghn->getDistricts($provinceId);
        if (isset($res['code']) && $res['code'] === 200 && !empty($res['data'])) {
            return response()->json($res);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success (Local Fallback)',
            'data' => VietnamLocationData::getDistricts($provinceId),
        ]);
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        $res = $ghn->getWards($districtId);
        if (isset($res['code']) && $res['code'] === 200 && !empty($res['data'])) {
            return response()->json($res);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success (Local Fallback)',
            'data' => VietnamLocationData::getWards($districtId),
        ]);
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        $cart = session('cart', []);
        $weight = collect($cart)->sum(
            fn ($item) => (int) ($item['weight'] ?? 500) * (int) $item['quantity']
        );

        $params = [
            'from_district_id' => (int) config('ghn.from_district_id', 3440),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
            'weight' => $weight > 0 ? $weight : 500,
            'length' => 20,
            'width' => 20,
            'height' => 15,
            'service_type_id' => 2,
        ];

        $res = $ghn->calculateFee($params);

        if (isset($res['code']) && $res['code'] === 200 && isset($res['data']['total'])) {
            return response()->json($res);
        }

        $fallbackFee = 30000;
        return response()->json([
            'code' => 200,
            'message' => 'Success (Fallback Fee)',
            'data' => [
                'total' => $fallbackFee,
                'service_fee' => $fallbackFee,
            ]
        ]);
    }
}
