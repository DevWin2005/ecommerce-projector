<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_displays_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('ĐĂNG NHẬP');
        $response->assertSee('Địa chỉ Email');
        $response->assertSee('Mật Khẩu');
    }
}
