<?php

namespace Unish;

/**
 * Cache command testing.
 *
 * @group commands
 */
class CacheCommandTest extends UnishIntegrationTestCase
{
    public function testCacheGet()
    {
        if ($this->isDrupalGreaterThanOrEqualTo('9.1.0@alpha')) {
            $this->markTestSkipped('Cache bins not working the same in Drupal 9.1+');
        }

        // Test the cache get command.
        $this->drush('cache-get', ['system.date', 'config'], ['format' => 'json']);
        $schema = $this->getOutputFromJSON('data');
        $this->assertNotEmpty($schema);

        // Test that get-ing a non-existant cid fails.
        $this->drush('cache-get', ['test-failure-cid'], ['format' => 'json'], self::EXIT_ERROR);
    }

    public function testCacheSet()
    {
        if ($this->isDrupalGreaterThanOrEqualTo('9.1.0@alpha')) {
            $this->markTestSkipped('Cache bins not working the same in Drupal 9.1+');
        }

        // Test setting a new cache item.
        $expected = 'cache test string';
        $this->drush('cache-set', ['cache-test-cid', $expected]);
        $this->drush('cache-get', ['cache-test-cid'], ['format' => 'json']);
        $data = $this->getOutputFromJSON('data');
        $this->assertEquals($expected, $data);

        // Test cache-set using all arguments and many options.
        $expected = ['key' => 'value'];
        $stdin = json_encode(['data' => $expected]);
        $bin = 'default';

        $this->drush('cache-set', ['my_cache_id', '-', $bin, 'CACHE_PERMANENT'], ['input-format' => 'json', 'cache-get' => true], self::EXIT_SUCCESS, $stdin);

        $this->drush('cache-get', ['my_cache_id'], ['format' => 'json']);
        $data = $this->getOutputFromJSON('data');
        $this->assertEquals($expected, $data);
    }
}
