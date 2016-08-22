<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockDeleted;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteContentBlockHandler
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ContentBlockRepository $contentBlockRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->contentBlockRepository = $contentBlockRepository;
    }

    /**
     * @param DeleteContentBlock $deleteContentBlock
     *
     * @return ContentBlock
     */
    public function handle(DeleteContentBlock $deleteContentBlock)
    {
        $this->contentBlockRepository->removeByIdAndLocale(
            $deleteContentBlock->contentBlock->getId(),
            $deleteContentBlock->contentBlock->getLocale()
        );

        Model::deleteExtraById($deleteContentBlock->contentBlock->getExtraId());

        $this->eventDispatcher->dispatch(
            ContentBlockDeleted::EVENT_NAME,
            new ContentBlockDeleted($deleteContentBlock->contentBlock)
        );
    }
}
