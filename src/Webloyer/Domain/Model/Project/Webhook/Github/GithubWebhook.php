<?php

declare(strict_types=1);

namespace Webloyer\Domain\Model\Project\Webhook\GitHub;

use Webloyer\Domain\Model\Project\ProjectInterest;
use Webloyer\Domain\Model\User\UserEmail;

class GitHubWebhook
{
    /** @var GitHubWebhookSecret|null */
    private $secret;
    /** @var UserEmail|null */
    private $executor;

    /**
     * @param string|null $secret
     * @param string|null $executor
     * @return self
     */
    public static function of(
        ?string $secret,
        ?string $executor
    ): self {
        return new self(
            isset($secret) ? new GitHubWebhookSecret($secret) : null,
            isset($executor) ? new UserEmail($executor) : null
        );
    }

    /**
     * @param GitHubWebhookSecret|null $secret
     * @param UserEmail|null              $executor
     * @return void
     */
    public function __construct(
        ?GitHubWebhookSecret $secret,
        ?UserEmail $executor
    ) {
        $this->secret = $secret;
        $this->executor = $executor;
    }

    /**
     * @return string|null
     */
    public function secret(): ?string
    {
        return isset($this->secret) ? $this->secret->value() : null;
    }

    /**
     * @return string|null
     */
    public function executor(): ?string
    {
        return isset($this->executor) ? $this->executor->value() : null;
    }

    /**
     * @param ProjectInterest $interest
     * @return void
     */
    public function provide(ProjectInterest $interest): void
    {
        $interest->informGitHubWebhookSecret($this->secret());
        $interest->informGitHubWebhookExecutor($this->executor());
    }
}
