<?php

namespace Src\BlendedConcept\StoryBook\Application\UseCases\Commands;

use Src\BlendedConcept\StoryBook\Domain\Model\StoryBook;
use Src\BlendedConcept\StoryBook\Domain\Repositories\StoryBookRepositoryInterface;
use Src\Common\Domain\CommandInterface;

class CreateStoreStoryBookCommand implements CommandInterface
{
    private StoryBookRepositoryInterface $repository;

    public function __construct(
        private readonly StoryBook $storyBook
    ) {
        $this->repository = app()->make(StoryBookRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->createStoryBook($this->storyBook);
    }
}
