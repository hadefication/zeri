<?php

use App\Generators\BaseGenerator;
use Illuminate\Support\Facades\File;

it('can list available commands', function () {
    $this->artisan('list')
        ->expectsOutputToContain('init')
        ->expectsOutputToContain('add-spec')
        ->expectsOutputToContain('generate')
        ->expectsOutputToContain('migrate')
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

    // Verify new structure was created
    expect(File::exists($testDir.'/.zeri'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/ZERI.md'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/specs'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/templates'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/templates/spec.md'))->toBeTrue();

    // Verify ZERI.md contains expected content
    $zeriContent = File::get($testDir.'/.zeri/ZERI.md');
    expect($zeriContent)->toContain('Test Project');
    expect($zeriContent)->toContain('Instructions for AI Assistants');
    expect($zeriContent)->toContain('Specification Workflow');

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
    $this->artisan('add-spec', ['name' => 'test-feature', '--path' => $testDir, '--no-branch' => true])
        ->expectsQuestion('Brief overview of this feature', 'A test feature')
        ->expectsOutput("✅ Specification 'test-feature' created successfully!")
        ->assertSuccessful();

    // Verify spec was created
    expect(File::exists($testDir.'/.zeri/specs/test-feature.md'))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});

it('can generate AI files with reference injection', function () {
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
        ->expectsOutput('✅ Updated: CLAUDE.md')
        ->expectsOutput('✅ Updated: GEMINI.md')
        ->expectsOutput('✅ Updated: .cursor/rules/zeri.mdc')
        ->expectsOutput('✅ Updated: AGENTS.md')
        ->assertSuccessful();

    // Verify files were created with reference
    $claudePath = $testDir.'/CLAUDE.md';
    $geminiPath = $testDir.'/GEMINI.md';
    $codexPath = $testDir.'/AGENTS.md';
    $cursorPath = $testDir.'/.cursor/rules/zeri.mdc';

    expect(File::exists($claudePath))->toBeTrue();
    expect(File::exists($geminiPath))->toBeTrue();
    expect(File::exists($codexPath))->toBeTrue();
    expect(File::exists($cursorPath))->toBeTrue();

    // Verify all files contain the reference
    $reference = BaseGenerator::ZERI_REFERENCE;
    expect(File::get($claudePath))->toContain($reference);
    expect(File::get($geminiPath))->toContain($reference);
    expect(File::get($codexPath))->toContain($reference);
    expect(File::get($cursorPath))->toContain($reference);

    // Cleanup
    File::deleteDirectory($testDir);
});

it('does not duplicate reference on repeated generate', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    // Initialize
    $this->artisan('init', ['--path' => $testDir, '--yes' => true])
        ->assertSuccessful();

    // Generate first time
    $this->artisan('generate', ['ai' => 'claude', '--path' => $testDir])
        ->expectsOutput('✅ Updated: CLAUDE.md')
        ->assertSuccessful();

    // Generate second time - should skip
    $this->artisan('generate', ['ai' => 'claude', '--path' => $testDir])
        ->expectsOutputToContain('Skipped: CLAUDE.md (reference already exists)')
        ->assertSuccessful();

    // Verify reference appears only once
    $claudeContent = File::get($testDir.'/CLAUDE.md');
    $reference = BaseGenerator::ZERI_REFERENCE;
    $count = substr_count($claudeContent, $reference);
    expect($count)->toBe(1);

    // Cleanup
    File::deleteDirectory($testDir);
});

it('can migrate from old structure to new structure', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);
    mkdir($testDir.'/.zeri');
    mkdir($testDir.'/.zeri/specs');
    mkdir($testDir.'/.zeri/templates');

    // Create old structure files
    File::put($testDir.'/.zeri/project.md', "# Test Project\n\nProject description here.");
    File::put($testDir.'/.zeri/development.md', "# Development\n\nDevelopment practices here.");

    // Run migrate
    $this->artisan('migrate', ['--path' => $testDir])
        ->expectsOutput('Migration complete!')
        ->assertSuccessful();

    // Verify new structure
    expect(File::exists($testDir.'/.zeri/ZERI.md'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/project.md'))->toBeFalse();
    expect(File::exists($testDir.'/.zeri/development.md'))->toBeFalse();

    // Verify content was merged
    $zeriContent = File::get($testDir.'/.zeri/ZERI.md');
    expect($zeriContent)->toContain('Project description here');
    expect($zeriContent)->toContain('Development practices here');

    // Cleanup
    File::deleteDirectory($testDir);
});

it('can migrate with backup option', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);
    mkdir($testDir.'/.zeri');
    mkdir($testDir.'/.zeri/specs');
    mkdir($testDir.'/.zeri/templates');

    // Create old structure files
    File::put($testDir.'/.zeri/project.md', "# Test Project\n\nProject description here.");
    File::put($testDir.'/.zeri/development.md', "# Development\n\nDevelopment practices here.");

    // Run migrate with backup
    $this->artisan('migrate', ['--path' => $testDir, '--backup' => true])
        ->expectsOutput('Migration complete!')
        ->assertSuccessful();

    // Verify backup files exist
    expect(File::exists($testDir.'/.zeri/project.md.bak'))->toBeTrue();
    expect(File::exists($testDir.'/.zeri/development.md.bak'))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});

it('refuses to generate with old structure', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);
    mkdir($testDir.'/.zeri');

    // Create old structure files
    File::put($testDir.'/.zeri/project.md', '# Test Project');
    File::put($testDir.'/.zeri/development.md', '# Development');

    // Try to generate - should fail
    $this->artisan('generate', ['ai' => 'claude', '--path' => $testDir])
        ->expectsOutput('Old zeri structure detected (project.md + development.md)')
        ->assertExitCode(1);

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

it('supports append position option', function () {
    $testDir = '/tmp/zeri-test-'.uniqid();
    mkdir($testDir);

    // Initialize
    $this->artisan('init', ['--path' => $testDir, '--yes' => true])
        ->assertSuccessful();

    // Create an existing CLAUDE.md with some content
    File::put($testDir.'/CLAUDE.md', "# Existing Content\n\nSome existing instructions.");

    // Generate with append position
    $this->artisan('generate', ['ai' => 'claude', '--path' => $testDir, '--position' => 'append'])
        ->expectsOutput('✅ Updated: CLAUDE.md')
        ->assertSuccessful();

    // Verify reference is at the end
    $claudeContent = File::get($testDir.'/CLAUDE.md');
    $reference = BaseGenerator::ZERI_REFERENCE;

    expect($claudeContent)->toContain($reference);
    expect(str_starts_with($claudeContent, '# Existing Content'))->toBeTrue();
    expect(str_ends_with(trim($claudeContent), $reference))->toBeTrue();

    // Cleanup
    File::deleteDirectory($testDir);
});
