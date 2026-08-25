<?php

namespace Tests\Unit;

use App\Services\TemplateBridge;
use PHPUnit\Framework\TestCase;

class TemplateBridgeTest extends TestCase
{
    public function test_lang_placeholder(): void
    {
        $this->assertStringContainsString(
            "__('quickad.hello')",
            (new TemplateBridge)->convert('<h1>{LANG_HELLO}</h1>')
        );
    }

    public function test_link_placeholder(): void
    {
        $this->assertStringContainsString(
            "\$link['LOGIN']",
            (new TemplateBridge)->convert('<a href="{LINK_LOGIN}">go</a>')
        );
    }

    public function test_if_block(): void
    {
        $out = (new TemplateBridge)->convert("IF('{X}'!=''){\n<p>yes</p>\n{:IF}");
        $this->assertStringContainsString('@if(', $out);
        $this->assertStringContainsString('@endif', $out);
    }

    public function test_loop_block(): void
    {
        $out = (new TemplateBridge)->convert('<!-- BEGIN LOOP items --><li>{item.title}</li><!-- END LOOP -->');
        $this->assertStringContainsString('@foreach', $out);
        $this->assertStringContainsString('@endforeach', $out);
        $this->assertStringContainsString("\$item['title']", $out);
    }

    public function test_plain_var(): void
    {
        $this->assertStringContainsString(
            "{{ \$username ?? '' }}",
            (new TemplateBridge)->convert('<b>{USERNAME}</b>')
        );
    }
}
