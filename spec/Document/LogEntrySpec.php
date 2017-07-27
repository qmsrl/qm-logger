<?php

namespace spec\QM\Logger\Document;

use Equip\Structure\Dictionary;
use Equip\Structure\UnorderedList;
use QM\Logger\Document\LogEntry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use QM\Logger\Model\LogModel;

class LogEntrySpec extends ObjectBehavior
{
    function it_is_initializable_with_array()
    {
        $fixture = ['entryType' => 'data', 'content' => new \DateTime()];
        $this->beConstructedThrough("create", [$fixture]);
        $this->shouldHaveType(LogEntry::class);
    }

    function it_is_initializable_with_dictionary()
    {
        $fixture = ['entryType' => 'data', 'content' => new \DateTime()];
        $this->beConstructedThrough("create", [new Dictionary($fixture)]);
        $this->shouldHaveType(LogEntry::class);
    }

    function it_could_compose()
    {
        $now = new \DateTime();
        $fixture = ['entryType' => 'date', 'content' => $now];
        $fixture2 = ['entryType' => 'state', 'content' => 'raise'];

        $entry = LogEntry::create(new Dictionary($fixture));
        $entry2 = LogEntry::create(new Dictionary($fixture2));

        $this->beConstructedThrough("compose", [[$entry, $entry2]]);
        $this->shouldHaveType(LogEntry::class);

        $this->getEntry()->getValue("state")->shouldBe("raise");
        $this->getEntry()->getValue("date")->shouldBe($now);
    }

    function it_could_compose_with_multiple()
    {
        $now = new \DateTime();
        $fixture = ['entryType' => 'date', 'content' => $now];
        $fixture2 = ['entryType' => 'state', 'content' => 'raise'];
        $fixture3 = ['entryType' => 'trigger', 'content' => 'exception'];
        $fixture4 = ['entryType' => 'comment', 'content' => 'messaggio eccezione'];

        $entry3 = LogEntry::compose([LogEntry::create(new Dictionary($fixture)), LogEntry::create(new Dictionary($fixture2))]);
        $entry4 = LogEntry::compose([LogEntry::create(new Dictionary($fixture3)), LogEntry::create(new Dictionary($fixture4))]);

        $this->beConstructedThrough("compose", [[$entry3, $entry4]]);
        $this->shouldHaveType(LogEntry::class);

        $this->getEntry()->getValue("state")->shouldBe("raise");
        $this->getEntry()->getValue("date")->shouldBe($now);
        $this->getEntry()->getValue("trigger")->shouldBe('exception');
        $this->getEntry()->getValue("comment")->shouldBe('messaggio eccezione');
    }

    function it_could_compose_with_multiple_and_merge_right()
    {
        $now = new \DateTime();
        $fixture1 = ['entryType' => 'date', 'content' => $now];
        $fixture2 = ['entryType' => 'state', 'content' => 'raise'];
        $fixture3 = ['entryType' => 'state', 'content' => 'messaggio eccezione'];

        $entry3 = LogEntry::create(new Dictionary($fixture1));
        $entry4 = LogEntry::compose([LogEntry::create(new Dictionary($fixture2)), LogEntry::create(new Dictionary($fixture3))]);

        $this->beConstructedThrough("compose", [[$entry3, $entry4]]);
        $this->shouldHaveType(LogEntry::class);

        $this->getEntry()->getValue("state")->shouldBe('messaggio eccezione');
        $this->getEntry()->getValue("date")->shouldBe($now);
    }

    function it_could_be_created_by_simple_array()
    {
        $now = new \DateTime("now");
        $fixture = [
            'date' => $now,
            'state' => 'amqp message',
            'trigger' => 'receive'
        ];

        $model = LogModel::create(new UnorderedList([
            [true => ['date' => \DateTime::class]],
            [true => ['state' => 'string']],
            [true => ['trigger' => 'string']]
        ]));
        $this->beConstructedThrough("build", [$fixture, $model]);
        $this->getEntry()->getValue("state")->shouldBe('amqp message');
        $this->getEntry()->getValue("trigger")->shouldBe('receive');
        $this->getEntry()->getValue("date")->shouldBe($now);
    }
}
