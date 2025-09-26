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
    $this->artisan('add-spec', ['name' => 'test-feature', '--path' => $testDir, '--no-branch' => true])
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
        ->expectsOutput('✅ Generated: AGENTS.md')
        ->assertSuccessful();

    // Verify files were created
    $claudePath = $testDir.'/CLAUDE.md';
    $geminiPath = $testDir.'/GEMINI.md';
    $codexPath = $testDir.'/AGENTS.md';
    $cursorPath = $testDir.'/.cursor/rules/zeri.mdc';

    expect(File::exists($claudePath))->toBeTrue();
    expect(File::exists($geminiPath))->toBeTrue();
    expect(File::exists($codexPath))->toBeTrue();
    expect(File::exists($cursorPath))->toBeTrue();

    $pattern = '/## ⚠️ Specification Workflow\s+.*?\*\*TODO Best Practices\*\*\s+```markdown\s+## TODO.*?```/s';

    expect(preg_match($pattern, File::get($claudePath), $claudeMatches))->toBe(1);
    expect(preg_match($pattern, File::get($geminiPath), $geminiMatches))->toBe(1);
    expect(preg_match($pattern, File::get($codexPath), $codexMatches))->toBe(1);
    expect(preg_match($pattern, File::get($cursorPath), $cursorMatches))->toBe(1);

    $claudeBlock = trim($claudeMatches[0]);
    $geminiBlock = trim($geminiMatches[0]);
    $codexBlock = trim($codexMatches[0]);
    $cursorBlock = trim($cursorMatches[0]);

    expect($claudeBlock)->toBe($geminiBlock);
    expect($claudeBlock)->toBe($codexBlock);
    expect($claudeBlock)->toBe($cursorBlock);
    expect($claudeBlock)->toContain('Always use `zeri add-spec <name>` to create new feature specifications—never create these files manually.');
    expect($claudeBlock)->toContain('Do not implement any specification unless the user explicitly instructs you to begin coding.');
    expect($claudeBlock)->toContain('Update TODOs in real time—mark each checkbox immediately after its implementation step finishes so progress is never batched at the end.');

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
