<?php

declare(strict_types=1);

namespace Pest\Configuration;




final class Project
{





public string $assignees = '';






public string $issues = '';






public string $prs = '';




private static ?self $instance = null;




public static function getInstance(): self
{
return self::$instance ??= new self;
}




public function github(string $project): self
{
$this->issues = "https://github.com/{$project}/issues/%s";
$this->prs = "https://github.com/{$project}/pull/%s";

$this->assignees = 'https://github.com/%s';

return $this;
}




public function gitlab(string $project): self
{
$this->issues = "https://gitlab.com/{$project}/issues/%s";
$this->prs = "https://gitlab.com/{$project}/merge_requests/%s";

$this->assignees = 'https://gitlab.com/%s';

return $this;
}




public function bitbucket(string $project): self
{
$this->issues = "https://bitbucket.org/{$project}/issues/%s";
$this->prs = "https://bitbucket.org/{$project}/pull-requests/%s";

$this->assignees = 'https://bitbucket.org/%s';

return $this;
}




public function jira(string $namespace, string $project): self
{
$this->issues = "https://{$namespace}.atlassian.net/browse/{$project}-%s";

$this->assignees = "https://{$namespace}.atlassian.net/secure/ViewProfile.jspa?name=%s";

return $this;
}




public function custom(string $issues, string $prs, string $assignees): self
{
$this->issues = $issues;
$this->prs = $prs;

$this->assignees = $assignees;

return $this;
}
}
