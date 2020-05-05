<?php

declare(strict_types=1);

namespace Webloyer\Domain\Model\Project;

class Projects
{
    /** @var array<int, Project> */
    private $projects;

    /**
     * @param Project ...$projects
     * @return void
     */
    public function __construct(Project ...$projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return array<int, Project>
     */
    public function toArray(): array
    {
        return $this->projects;
    }
}