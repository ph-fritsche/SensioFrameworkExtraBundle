<?php

namespace Sensio\Bundle\FrameworkExtraBundle\Cache;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 *
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class AnnotationCacheListener
{
    /**
     * Modifies the response to apply HTTP expiration header fields.
     */
    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (!$configuration = $event->getRequest()->attributes->get('_cache')) {
            return;
        }

        $response = $event->getResponse();

        if (!$response->isSuccessful()) {
            return;
        }

        if (null !== $configuration->getSMaxAge()) {
            $response->setSharedMaxAge($configuration->getSMaxAge());
        }

        if (null !== $configuration->getMaxAge()) {
            $response->setMaxAge($configuration->getMaxAge());
        }

        if (null !== $configuration->getExpires()) {
            $date = \DateTime::createFromFormat('U', strtotime($configuration->getExpires()), new \DateTimeZone('UTC'));
            $response->setExpires($date);
        }

        if ($configuration->isPublic()) {
            $response->setPublic();
        }

        $event->setResponse($response);
    }
}
