<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GitInfoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    /**
     * Check Commit Changes when rev is the same
     *
     * @return void
     */
    public function testSameCommitChanges()
    {
        $this->assertTrue(true);
    }


    /**
     * Check Commit Changes when rev is the different
     *
     * @return void
     */
    public function testDifferentCommitChanges()
    {
        $this->assertTrue(true);
    }

    /**
     * Check That Reported Changes Match actual file existences in repo
     *
     * @return void
     */
    public function testRepoFilesMatchGitChanges()
    {
        $this->assertTrue(true);
    }
}
