<?php

namespace spec\QM\Logger\Model;

use Equip\Structure\Dictionary;
use Equip\Structure\UnorderedList;
use QM\Logger\Model\LogModel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LogModelSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('create', [new UnorderedList()]);
        $this->shouldHaveType(LogModel::class);
    }

    function it_validate_model_from_create()
    {
        $fixture = [
            [true => ['date' => \DateTime::class]],
            [true => ['state' => 'string']],
            [true => ['trigger' => 'string']],
            [false => ['data' => Dictionary::class]],
            [false => ['comment' => 'string']],
            [false => ['flags' => Dictionary::class]]
        ];

        $this->beConstructedThrough('create', [new UnorderedList($fixture)]);
        $this->shouldHaveType(LogModel::class);
    }



}
