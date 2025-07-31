<?php

use Illuminate\Support\Facades\File;

it('can list available commands', function () {
    $this->artisan('list')
        ->expectsOutputToContain('init')
        ->expectsOutputToContain('add-spec')
        ->expectsOutputToContain('generate')
        ->assertSuccessful();
});

it('can initialize a project structure', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    $this->artisan('init', ['--path' => $testDir])
        ->expectsQuestion('Project name', 'Test Project')
        ->expectsQuestion('Project description', 'A test project')
        ->expectsQuestion('Primary tech stack', 'PHP, Laravel')
        ->expectsQuestion('Current development focus', 'Testing')
        ->expectsOutput('✅ Zeri project structure initialized successfully!')
        ->assertSuccessful();

    // Verify structure was created
    expect(File::exists($testDir.'/.zeri'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/project.md'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/development.md'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/specs'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/templates'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/templates/spec.md'))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});

it('can add a specification file', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    // First initialize
    $this->artisan('init', ['--path' => $testDir])
        ->expectsQuestion('Project name', 'Test Project')
        ->expectsQuestion('Project description', 'A test project')
        ->expectsQuestion('Primary tech stack', 'PHP, Laravel')
        ->expectsQuestion('Current development focus', 'Testing')
        ->assertSuccessful();

    // Then add spec
    $this->artisan('add-spec', ['name' => 'test-feature', '--path' => $testDir])
        ->expectsQuestion('Brief overview of this feature', 'A test feature')
        ->expectsOutput("✅ Specification 'test-feature' created successfully!")
        ->assertSuccessful();

    // Verify spec was created
    expect(File::exists($testDir.'/.zeri/specs/test-feature.md'))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});

it('can generate AI files', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    // First initialize
    $this->artisan('init', ['--path' => $testDir])
        ->expectsQuestion('Project name', 'Test Project')
        ->expectsQuestion('Project description', 'A test project')
        ->expectsQuestion('Primary tech stack', 'PHP, Laravel')
        ->expectsQuestion('Current development focus', 'Testing')
        ->assertSuccessful();

    // Generate all AI files
    $this->artisan('generate', ['ai' => 'all', '--path' => $testDir])
        ->expectsOutput('✅ Generated: CLAUDE.md')
        ->expectsOutput('✅ Generated: GEMINI.md')
        ->expectsOutput('✅ Generated: .cursor/rules/zeri.mdc')
        ->assertSuccessful();

    // Verify files were created
    expect(File::exists($testDir.'/CLAUDE.md'))->toBeTrue();
    expect(File::exists($testDir.'/GEMINI.md'))->toBeTrue();
    expect(File::exists($testDir.'/.cursor/rules/zeri.mdc'))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});

it('handles errors when .zeri directory does not exist', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    $this->artisan('add-spec', ['name' => 'test', '--path' => $testDir])
        ->expectsOutput('.zeri directory not found. Run "zeri init" first.')
        ->assertExitCode(1);

    $this->artisan('generate', ['ai' => 'claude', '--path' => $testDir])
        ->expectsOutput('.zeri directory not found. Run "zeri init" first.')
        ->assertExitCode(1);

    // Cleanup
    rmdir($testDir);
});
