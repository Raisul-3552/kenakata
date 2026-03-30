<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────────
        Schema::create('Admin', function (Blueprint $table) {
            $table->integer('AdminID')->primary();
            $table->string('AdminName', 255);
            $table->string('Email', 255)->unique();
            $table->string('Password', 255);
        });

        // ── EmploymentCode ────────────────────────────────────────────────────
        Schema::create('EmploymentCode', function (Blueprint $table) {
            $table->integer('CodeID', true);
            $table->string('RegCode', 100)->unique();
            $table->integer('AdminID');
            $table->boolean('IsUsed')->default(0);
            $table->dateTime('CreatedAt')->useCurrent();

            $table->foreign('AdminID', 'FK_Code_Admin')
                  ->references('AdminID')
                  ->on('Admin');
        });

        // ── Employee ──────────────────────────────────────────────────────────
        Schema::create('Employee', function (Blueprint $table) {
            $table->integer('EmployeeID', true);
            $table->string('EmployeeName', 255);
            $table->string('Phone', 20);
            $table->string('Email', 255)->unique()->nullable();
            $table->string('Password', 255);
            $table->string('Address', 500);
            $table->integer('CodeID');

            $table->foreign('CodeID', 'FK_Employee_Code')
                  ->references('CodeID')
                  ->on('EmploymentCode');
        });

        // ── Customer ──────────────────────────────────────────────────────────
        Schema::create('Customer', function (Blueprint $table) {
            $table->integer('CustomerID', true);
            $table->string('CustomerName', 255);
            $table->string('Phone', 20);
            $table->string('Email', 255)->unique()->nullable();
            $table->string('Password', 255);
            $table->string('Address', 500);
        });

        // ── DeliveryMan ───────────────────────────────────────────────────────
        Schema::create('DeliveryMan', function (Blueprint $table) {
            $table->integer('DelManID', true);
            $table->string('DelManName', 255);
            $table->string('Phone', 20);
            $table->string('Email', 255)->unique()->nullable();
            $table->string('Password', 255);
            $table->string('Address', 500);
        });

        // ── UserSessions ──────────────────────────────────────────────────────
        Schema::create('UserSessions', function (Blueprint $table) {
            $table->string('SessionID', 255)->primary();
            $table->integer('UserID');
            $table->string('UserType', 50);
            $table->string('IPAddress', 45)->nullable();
            $table->text('UserAgent')->nullable();
            $table->text('Payload');
            $table->integer('LastActivity');
            $table->dateTime('CreatedAt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserSessions');
        Schema::dropIfExists('DeliveryMan');
        Schema::dropIfExists('Customer');
        Schema::dropIfExists('Employee');
        Schema::dropIfExists('EmploymentCode');
        Schema::dropIfExists('Admin');
    }
};
