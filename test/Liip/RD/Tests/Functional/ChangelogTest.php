<?php

namespace Liip\RD\Tests\Functional;


class ChangelogTest extends RDFunctionalTestBase
{
    public function testSimple()
    {
        $this->createChangelog('simple');
        $this->createJsonConfig("simple", "changelog");
        $this->executeTest(null, 'comment1', '1');
        $this->executeTest(null, 'comment2', '2');
    }

    public function testSemantic()
    {
        $this->createChangelog('semantic');
        $this->createJsonConfig("semantic", "changelog");
        $this->executeTest('major', 'First major', '1.0.0');
        $this->executeTest('patch', 'First patch', '1.0.1');
        $this->executeTest('minor', 'First minor', '1.1.0');
        $this->executeTest('major', 'Second major', '2.0.0');
        $this->executeTest('minor', 'test_minor', '2.1.0');
    }

    protected function createChangelog($format)
    {
        $file = $this->tempDir.'/CHANGELOG';
        touch($file);
        $manager = new \Liip\RD\Changelog\ChangelogManager($file, $format);
        $manager->update(
            $format=='semantic' ? '0.0.1' : '1',
            'First release',
            $format=='semantic' ? array('type'=>'patch') : null
        );
    }

    /**
     * Execute changelog test
     * @param String [major/minor/patch]
     * @param String comment
     * @param String expected version number (ie 2.0.0)
     */
    protected function executeTest($semanticType, $comment, $expectedVersion)
    {
//        $this->manualDebug();
        if (is_null($semanticType)) {
            exec('./RD release -n --comment="'.$comment.'"');
        } else {
            exec('./RD release -n --type='.$semanticType.' --comment="'.$comment.'"');
        }
        $changelog = file_get_contents($this->tempDir.'/CHANGELOG');
        $this->assertRegExp('/'.$expectedVersion.'/',$changelog);
        $this->assertRegExp('/'.$comment.'/',$changelog);
    }

}

