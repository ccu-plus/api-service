<?php

declare(strict_types=1);

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

final class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testExample(): void
    {
        $this->get('/');

        $this->assertSame(
            $this->app->version(), $this->response->getContent()
        );
    }
}
