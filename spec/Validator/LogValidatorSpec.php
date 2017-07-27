<?php

namespace spec\QM\Logger\Validator;

use Equip\Structure\Dictionary;
use Equip\Structure\UnorderedList;
use QM\Logger\Document\LogEntry;
use QM\Logger\Model\LogModel;
use QM\Logger\Validator\LogValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LogValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LogValidator::class);
    }

    function it_should_validate_schema()
    {
        $fixture1 = ['entryType' => 'date', 'content' => new \DateTime()];
        $fixture2 = ['entryType' => 'state', 'content' => 'bla bla bla'];
        $fixture3 = ['entryType' => 'trigger', 'content' => 'bla bla bla'];
        $fixture4 = ['entryType' => 'data', 'content' => new Dictionary(['data' => 'bla bla bla'])];
        $fixture5 = ['entryType' => 'comment', 'content' => 'bla bla bla'];
        $fixture6 = ['entryType' => 'flags', 'content' => new Dictionary(['data' => 'bla bla bla'])];

        $entry = LogEntry::compose([
            LogEntry::create(new Dictionary($fixture1)),
            LogEntry::create(new Dictionary($fixture2)),
            LogEntry::create(new Dictionary($fixture3)),
            LogEntry::create(new Dictionary($fixture4)),
            LogEntry::create(new Dictionary($fixture5)),
            LogEntry::create(new Dictionary($fixture6))]);

        $array = [
            [false => ['date' => \DateTime::class]],
            [false => ['state' => 'string']],
            [false => ['trigger' => 'string']],
            [false => ['data' => 'array']],
            [false => ['comment' => 'string']],
            [false => ['flags' => 'array']]
        ];

        $model = LogModel::create(new UnorderedList($array));

        $this->validate($model, $entry)->shouldBe(true);
    }

    function it_should_not_validate_schema()
    {
        $fixture1 = ['entryType' => 'date', 'content' => new \DateTime()];
        $fixture2 = ['entryType' => 'state', 'content' => 'bla bla bla'];
        $fixture3 = ['entryType' => 'trigger', 'content' => 'bla bla bla'];
        $fixture4 = ['entryType' => 'data', 'content' => 21];
        $fixture5 = ['entryType' => 'comment', 'content' => 'bla bla bla'];
        $fixture6 = ['entryType' => 'flags', 'content' => new Dictionary(['data' => 'bla bla bla'])];

        $entry = LogEntry::compose([
            LogEntry::create(new Dictionary($fixture1)),
            LogEntry::create(new Dictionary($fixture2)),
            LogEntry::create(new Dictionary($fixture3)),
            LogEntry::create(new Dictionary($fixture4)),
            LogEntry::create(new Dictionary($fixture5)),
            LogEntry::create(new Dictionary($fixture6))]);

        $array = [
            [false => ['date' => \DateTime::class]],
            [false => ['state' => 'string']],
            [false => ['trigger' => 'string']],
            [false => ['data' => 'array']],
            [false => ['comment' => 'string']],
            [false => ['flags' => 'array']]
        ];

        $model = LogModel::create(new UnorderedList($array));

        $this->validate($model, $entry)->shouldBe(false);
    }

    function it_should_not_validate_schema_with_required()
    {
        $fixture1 = ['entryType' => 'date', 'content' => new \DateTime()];

        $entry = LogEntry::compose([
            LogEntry::create(new Dictionary($fixture1))
        ]);

        $array = [
            [false => ['date' => \DateTime::class]],
            [true => ['state' => 'string']]
        ];

        $model = LogModel::create(new UnorderedList($array));

        $this->validate($model, $entry)->shouldBe(false);
    }

    function it_should_validate_schema_with_required()
    {
        $fixture1 = ['entryType' => 'date', 'content' => new \DateTime()];
        $fixture2 = ['entryType' => 'state', 'content' => "bla bla bal"];

        $entry = LogEntry::compose([
            LogEntry::create(new Dictionary($fixture1)),
            LogEntry::create(new Dictionary($fixture2)),
        ]);

        $array = [
            [false => ['date' => \DateTime::class]],
            [true => ['state' => 'string']]
        ];

        $model = LogModel::create(new UnorderedList($array));

        $this->validate($model, $entry)->shouldBe(true);
    }
}
