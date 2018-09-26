<?php

namespace SlashTrace\Bugsnag\Tests;

use SlashTrace\Bugsnag\BugsnagHandler;
use SlashTrace\Context\User;
use SlashTrace\EventHandler\EventHandlerException;

use Bugsnag\Client as Bugsnag;
use Bugsnag\Report as BugsnagReport;
use Bugsnag\Breadcrumbs\Breadcrumb as BugsnagBreadcrumb;
use PHPUnit\Framework\TestCase;

use Exception;

class BugsnagHandlerTest extends TestCase
{
    public function testExceptionIsPassedToBugsnagClient()
    {
        $exception = new Exception();

        $bugsnag = $this->createMock(Bugsnag::class);
        $bugsnag->expects($this->once())
            ->method("notifyException")
            ->with($exception);

        $handler = new BugsnagHandler($bugsnag);
        $handler->handleException($exception);
    }

    public function testBugsnagExceptionsAreHandled()
    {
        $originalException = new Exception();
        $bugsnagException = new Exception();

        $bugsnag = $this->createMock(Bugsnag::class);
        $bugsnag->expects($this->once())
            ->method("notifyException")
            ->with($originalException)
            ->willThrowException($bugsnagException);

        $handler = new BugsnagHandler($bugsnag);
        try {
            $handler->handleException($originalException);
            $this->fail("Expected exception: " . EventHandlerException::class);
        } catch (EventHandlerException $e) {
            $this->assertSame($bugsnagException, $e->getPrevious());
        }
    }

    public function testUserIsPassedToBugsnagClient()
    {
        $user = new User();
        $user->setId(12345);
        $user->setEmail("pfry@planetexpress.com");
        $user->setName("Philip J. Fry");

        $bugsnag = $this->createMock(Bugsnag::class);
        $bugsnag->expects($this->once())
            ->method("registerCallback")
            ->with($this->callback(function (callable $callback) use ($user) {
                $report = $this->createMock(BugsnagReport::class);
                $report->expects($this->once())
                    ->method("setUser")
                    ->with([
                        "id"    => $user->getId(),
                        "name"  => $user->getName(),
                        "email" => $user->getEmail()
                    ]);

                $callback($report);

                return true;
            }));

        $handler = new BugsnagHandler($bugsnag);
        $handler->setUser($user);
    }

    public function testBreadcrumbsArePassedToBugsnagClient()
    {
        $bugsnag = $this->createMock(Bugsnag::class);
        $bugsnag->expects($this->once())
            ->method("leaveBreadcrumb")
            ->with(
                "Something happened!",
                BugsnagBreadcrumb::MANUAL_TYPE,
                ["foo" => "bar"]
            );

        $handler = new BugsnagHandler($bugsnag);
        $handler->recordBreadcrumb("Something happened!", ["foo" => "bar"]);
    }
}