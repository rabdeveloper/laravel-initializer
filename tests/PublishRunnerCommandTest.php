<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use InvalidArgumentException;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderOne;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderTwo;

class PublishRunnerCommandTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_by_array($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class]);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_with_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'public']);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_with_wrong_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'wrong-tag']);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileNotExists($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_multiple_providers($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class,
                TestServiceProviderTwo::class,
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_multiple_providers_with_tags($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'public',
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function publish_multiple_providers_with_one_wrong_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'wrong-tag',
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileNotExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function force_publish_by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class, true);
        }, $command);

        clearstatcache();

        $this->assertTrue($last_update < filectime($public_path_to_file));

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function force_publish_by_array($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class]);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class], true);
        }, $command);

        clearstatcache();

        $this->assertTrue($last_update < filectime($public_path_to_file));

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function exception_on_invalid_argument($command)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->declareCommands(function (Run $run) {
            $run->publish(true);
        }, $command);
    }

    protected function getPackageProviders($app)
    {
        $providers = parent::getPackageProviders($app);

        array_push($providers, TestServiceProviderOne::class);
        array_push($providers, TestServiceProviderTwo::class);

        return $providers;
    }
}
