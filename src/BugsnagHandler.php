<?php

namespace SlashTrace\Bugsnag;

use SlashTrace\Context\User;
use SlashTrace\EventHandler\EventHandler;
use SlashTrace\EventHandler\EventHandlerException;

use Bugsnag\Client;
use Bugsnag\Report;
use Bugsnag\Breadcrumbs\Breadcrumb;

use Exception;

class BugsnagHandler implements EventHandler
{
    /** @var Client */
    private $bugsnag;

    public function __construct($bugsnag)
    {
        $this->bugsnag = $bugsnag instanceof Client ? $bugsnag : Client::make($bugsnag);
    }

    /**
     * @param Exception $exception
     * @return int
     * @throws EventHandlerException
     */
    public function handleException($exception)
    {
        try {
            $this->bugsnag->notifyException($exception);
        } catch (Exception $e) {
            throw new EventHandlerException($e->getMessage(), $e->getCode(), $e);
        }
        return EventHandler::SIGNAL_CONTINUE;
    }

    /**
     * @param User $user
     * @return void
     */
    public function setUser(User $user)
    {
        $this->bugsnag->registerCallback(function (Report $report) use ($user) {
            $report->setUser(array_filter([
                "id"    => $user->getId(),
                "email" => $user->getEmail(),
                "name"  => $user->getName()
            ]));
        });
    }

    /**
     * @param string $title
     * @param array $data
     * @return void
     */
    public function recordBreadcrumb($title, array $data = [])
    {
        $this->bugsnag->leaveBreadcrumb($title, Breadcrumb::MANUAL_TYPE, $data);
    }

    /**
     * @param string $release
     * @return void
     */
    public function setRelease($release)
    {
        // The release version cannot be set explicitly
        // Read the Bugsnag docs to see how release tracking is handled:
        // https://docs.bugsnag.com/platforms/php/other/#tracking-releases
    }

    /**
     * @param string $path
     * @return void
     */
    public function setApplicationPath($path)
    {
        // Local application path is currently not supported by the Bugsnag PHP SDK
    }
}