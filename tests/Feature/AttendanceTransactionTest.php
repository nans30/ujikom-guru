<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;

class AttendanceTransactionTest extends TestCase
{
    /**
     * Test absen gagal karena data tidak lengkap
     */
    public function test_absen_gagal_karena_data_tidak_lengkap(): void
    {
        // Simulasi login sebagai admin/user yang punya token
        $user = User::factory()->create();
        
        // Coba kirim data kosong/tidak lengkap ke endpoint attendance
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/attendances', [
            'teacher_id' => '', // Sengaja dikosongkan untuk men-trigger error validasi
            'date' => '',
            'status' => ''
        ]);

        // Ekspektasi: Validasi gagal (422)
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'errors'
                 ]);
    }

    /**
     * Test absen berhasil menggunakan rfid (mensimulasikan request API dengan sukses)
     */
    public function test_absen_berhasil_menggunakan_rfid(): void
    {
        // Simulasi login sebagai admin
        $user = User::factory()->create();
        
        // Buat dummy data teacher
        $teacher = Teacher::create([
            'name' => 'Guru Test RFID',
            'nip' => '1234567890',
            'email' => 'rfid@test.com',
            'is_active' => true,
            'created_by_id' => $user->id,
        ]);

        // Payload yang mensimulasikan tap RFID (misal metode hadir)
        $payload = [
            'teacher_id' => $teacher->id,
            'date' => date('Y-m-d'),
            'check_in' => '07:00:00',
            'status' => 'hadir',
            'method_in' => 'rfid', // simulasi metode RFID
        ];

        // Hit endpoint API untuk attendance
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/attendances', $payload);

        // Ekspektasi: Berhasil disimpan (200 OK)
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Attendance berhasil disimpan'
                 ]);

        // Verifikasi data masuk ke database
        $this->assertDatabaseHas('attendances', [
            'teacher_id' => $teacher->id,
            'status' => 'hadir',
            'method_in' => 'rfid'
        ]);
        
        // Cleanup dummy teacher setelan database default (jika RefreshDatabase tidak dicentang di base test)
        $teacher->delete();
    }
}
