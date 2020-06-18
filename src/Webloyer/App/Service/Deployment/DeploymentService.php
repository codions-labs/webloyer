<?php

declare(strict_types=1);

namespace Webloyer\App\Service\Deployment;

use Common\App\Service\ApplicationService;
use Webloyer\App\DataTransformer\Deployment\{
    DeploymentDataTransformer,
    DeploymentsDataTransformer,
};
use Webloyer\Domain\Model\Deployment\{
    Deployment,
    DeploymentDoesNotExistException,
    DeploymentNumber,
    DeploymentRepository,
};
use Webloyer\Domain\Model\Project\{
    Project,
    ProjectDoesNotExistException,
    ProjectId,
    ProjectRepository,
};
use Webloyer\Domain\Model\Recipe\{
    Recipe,
    RecipeId,
    RecipeRepository,
    Recipes,
};
use Webloyer\Domain\Model\Server\{
    ServerId,
    ServerRepository,
};
use Webloyer\Domain\Model\User\{
    UserId,
    UserRepository,
};

abstract class DeploymentService implements ApplicationService
{
    /** @var DeploymentRepository */
    protected $deploymentRepository;
    /** @var ProjectRepository */
    protected $projectRepository;
    /** @var RecipeRepository */
    protected $recipeRepository;
    /** @var ServerRepository */
    protected $serverRepository;
    /** @var UserRepository */
    protected $userRepository;
    /** @var DeploymentDataTransformer */
    protected $deploymentDataTransformer;
    /** @var DeploymentsDataTransformer */
    protected $deploymentsDataTransformer;

    /**
     * @param DeploymentRepository       $deploymentRepository
     * @param ProjectRepository          $projectRepository
     * @param RecipeRepository           $recipeRepository
     * @param ServerRepository           $serverRepository
     * @param UserRepository             $userRepository
     * @param DeploymentDataTransformer  $deploymentDataTransformer
     * @param DeploymentsDataTransformer $deploymentsDataTransformer
     * @return void
     */
    public function __construct(
        DeploymentRepository $deploymentRepository,
        ProjectRepository $projectRepository,
        RecipeRepository $recipeRepository,
        ServerRepository $serverRepository,
        UserRepository $userRepository,
        DeploymentDataTransformer $deploymentDataTransformer,
        DeploymentsDataTransformer $deploymentsDataTransformer
    ) {
        $this->deploymentRepository = $deploymentRepository;
        $this->projectRepository = $projectRepository;
        $this->recipeRepository = $recipeRepository;
        $this->serverRepository = $serverRepository;
        $this->userRepository = $userRepository;
        $this->deploymentDataTransformer = $deploymentDataTransformer;
        $this->deploymentsDataTransformer = $deploymentsDataTransformer;
    }

    /**
     * @return DeploymentDataTransformer
     */
    public function deploymentDataTransformer(): DeploymentDataTransformer
    {
        return $this->deploymentDataTransformer;
    }

    /**
     * @return DeploymentsDataTransformer
     */
    public function deploymentsDataTransformer(): DeploymentsDataTransformer
    {
        return $this->deploymentsDataTransformer;
    }

    /**
     * @param ProjectId $projectId
     * @return Project
     * @throws ProjectDoesNotExistException
     */
    protected function getNonNullProject(ProjectId $projectId): Project
    {
        $project = $this->projectRepository->findById($projectId);
        if (is_null($project)) {
            throw new ProjectDoesNotExistException(
                'Project does not exist.' . PHP_EOL .
                'Id: ' . $projectId->value()
            );
        }
        return $project;
    }

    /**
     * @param ProjectId $projectId
     * @param DeploymentNumber $number
     * @return Deployment
     * @throws DeploymentDoesNotExistException
     */
    protected function getNonNullDeployment(
        ProjectId $projectId,
        DeploymentNumber $number
    ): Deployment {
        $deployment = $this->deploymentRepository->findById($projectId, $number);
        if (is_null($deployment)) {
            throw new DeploymentDoesNotExistException(
                'Deployment does not exist.' . PHP_EOL .
                'Project Id: ' . $projectId->value() . PHP_EOL .
                'Number: ' . $number->value()
            );
        }
        return $deployment;
    }

    /**
     * @param Deployment $deployment
     * @return void
     */
    protected function requestDeployment(Deployment $deployment): void
    {
        $project = $this->projectRepository->findById(new ProjectId($deployment->projectId()));
        $recipes = new Recipes(...array_reduce($project->recipeIds(), function (array $carry, string $recipeId): array {
            $recipe = $this->recipeRepository->findById(new RecipeId($recipeId));
            if (is_null($recipe)) {
                return $carry;
            }
            $carry[] = $recipe;
            return $carry;
        }, []));
        $server = $this->serverRepository->findById(new ServerId($project->serverId()));
        $executor = $this->userRepository->findById(new UserId($deployment->executor()));

        $deployment->request(
            $project,
            $recipes,
            $server,
            $executor
        );
    }
}
