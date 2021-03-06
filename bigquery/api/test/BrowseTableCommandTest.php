<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Samples\BigQuery\Tests;

use Google\Cloud\Samples\BigQuery\BrowseTableCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Unit Tests for BrowseTableCommand.
 */
class BrowseTableCommandTest extends TestCase
{
    protected static $hasCredentials;

    public static function setUpBeforeClass()
    {
        $path = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        self::$hasCredentials = $path && file_exists($path) &&
            filesize($path) > 0;
    }

    public function testBrowseTable()
    {
        if (!self::$hasCredentials) {
            $this->markTestSkipped('No application credentials were found.');
        }
        if (!$projectId = getenv('GOOGLE_PROJECT_ID')) {
            $this->markTestSkipped('No project ID');
        }
        if (!$datasetId = getenv('GOOGLE_BIGQUERY_DATASET')) {
            $this->markTestSkipped('No bigquery dataset name');
        }
        if (!$tableId = getenv('GOOGLE_BIGQUERY_TABLE')) {
            $this->markTestSkipped('No bigquery table name');
        }

        $application = new Application();
        $application->add(new BrowseTableCommand());
        $commandTester = new CommandTester($application->get('browse-table'));
        $commandTester->execute(
            [
                'dataset.table' => $datasetId . '.' . $tableId,
                '--max-results' => 1,
                '--project' => $projectId,
            ],
            ['interactive' => false]
        );

        $this->expectOutputRegex('/Found \d+ row\(s\)/');
    }
}
