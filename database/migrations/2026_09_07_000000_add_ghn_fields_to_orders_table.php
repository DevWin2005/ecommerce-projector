<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status')->nullable()->after('status')->comment('Trạng thái vận chuyển GHN (ready_to_pick, delivering, delivered, cancel...)');
            $table->string('ghn_order_code')->nullable()->after('shipping_status')->comment('Mã vận đơn GHN trả về');
            $table->decimal('ghn_total_fee', 15, 2)->default(0)->after('ghn_order_code')->comment('Tiền cước vận chuyển GHN');
            $table->integer('to_district_id')->nullable()->after('ghn_total_fee')->comment('Mã quận/huyện để tính ship GHN');
            $table->string('to_ward_code')->nullable()->after('to_district_id')->comment('Mã phường/xã để tính ship GHN');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_status',
                'ghn_order_code',
                'ghn_total_fee',
                'to_district_id',
                'to_ward_code',
            ]);
        });
    }
};
