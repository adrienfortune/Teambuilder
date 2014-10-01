<?php namespace spec\Tm\TeambuilderBundle\Regles;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TeamBuilderSpec extends ObjectBehavior
{
    function let(array $listeRegles, array $listeChampion)
    {
    }

    function it_test()
    {
        $this->test()->shouldReturn('test');
    }
}