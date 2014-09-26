<?php namespace spec\Tm\TeambuilderBundle\Regles;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TeamBuilderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([], []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Tm\TeambuilderBundle\Regles\TeamBuilder');
    }
}