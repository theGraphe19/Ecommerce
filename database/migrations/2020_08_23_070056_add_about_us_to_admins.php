<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAboutUsToAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->text('about_us')->after('remember_token');
            $table->string('about_us_img')->after('about_us');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('about_us');
            $table->dropColumn('about_us_img');
        });
    }
}
