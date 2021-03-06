<?php

namespace Troopers\BehatContexts\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Troopers\BehatContexts\Collection\MailCollection;
use Troopers\BehatContexts\Mail\MailChecker;

/**
 * Class MailContext.
 */
class MailContext extends RawMinkContext implements Context, KernelAwareContext
{
    protected $container;
    /** @var MailCollection */
    protected $mailCollection;
    /** @var MailChecker */
    protected $mailChecker;

    /**
     * @param \Troopers\BehatContexts\Collection\MailCollection $mailCollection
     * @param \Troopers\BehatContexts\Mail\MailChecker          $mailChecker
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function initialize(MailCollection $mailCollection, MailChecker $mailChecker)
    {
        $this->mailCollection = $mailCollection;
        $mailChecker->setContainer($this->container);
        $this->mailChecker = $mailChecker;
    }

    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    /**
     * @Then I should have the email :event with:
     *
     * @param                               $event
     * @param \Behat\Gherkin\Node\TableNode $table
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
     */
    public function iShouldHaveTheEmailWith($event, TableNode $table)
    {
        $mailPrototype = $this->mailCollection->get($event);
        $this->mailChecker->check($mailPrototype, $table->getTable());
    }

    /**
     * @Given /^I follow the link "([^"]*)" for email ([^"]*) with:$/
     *
     * @param                               $link
     * @param                               $event
     * @param \Behat\Gherkin\Node\TableNode $table
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function iFollowTheLinkForEmailEventWith($link, $event, TableNode $table)
    {
        $mailPrototype = $this->mailCollection->get($event);
        $href = $this->mailChecker->getLink($mailPrototype, $table->getTable(), $link);
        $this->getSession()->visit($href);
    }
}
