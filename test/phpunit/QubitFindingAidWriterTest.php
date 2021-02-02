<?php

class QubitFindingAidWriterTest extends \PHPUnit\Framework\TestCase
{
  public function testSetResourceFromConstructor()
  {
    $resource = new QubitInformationObject;
    $writer = new QubitFindingAidWriter($resource);

    $this->assertSame($resource, $writer->getResource());
  }

  public function testSetResource()
  {
    $resource1 = new QubitInformationObject;
    $resource2 = new QubitInformationObject;
    $resource2->id = '11111';

    $writer = new QubitFindingAidWriter($resource1);

    $writer->setResource($resource2);

    $this->assertSame($resource2, $writer->getResource());
  }

  public function testSetResourceTypeError()
  {
    $writer = new QubitFindingAidWriter(new QubitInformationObject);

    $this->expectException(TypeError::class);
    $writer->setResource('foo');
  }

  public function testSetAppRoot()
  {
    $writer = new QubitFindingAidWriter(new QubitInformationObject);
    $writer->setAppRoot('/tmp/foo');

    $this->assertSame('/tmp/foo', $writer->getAppRoot());
  }

  public function testSetAppRootFromConstructorOptions()
  {
    $writer = new QubitFindingAidWriter(
      new QubitInformationObject,
      ['appRoot' => '/tmp/foo']
    );

    $this->assertSame('/tmp/foo', $writer->getAppRoot());
  }

  public function testSetLogger()
  {
    $logger = new sfNoLogger(new sfEventDispatcher());
    $writer = new QubitFindingAidWriter(new QubitInformationObject);

    $writer->setLogger($logger);

    $this->assertSame($logger, $writer->getLogger());
  }

  public function testSetLoggerFromConstructorOption()
  {
    $logger = new sfNoLogger(new sfEventDispatcher());
    $writer = new QubitFindingAidWriter(
      new QubitInformationObject,
      ['logger' => $logger]
    );

    $this->assertSame($logger, $writer->getLogger());
  }

  public function testGetModelDefaultValue()
  {
    $writer = new QubitFindingAidWriter(new QubitInformationObject);

    $this->assertSame('inventory-summary', $writer->getModel());
  }

  public function testSetModel()
  {
    $writer = new QubitFindingAidWriter(new QubitInformationObject);
    $writer->setModel('full-details');

    $this->assertSame('full-details', $writer->getModel());
  }

  public function testSetInvalidModel()
  {
    $writer = new QubitFindingAidWriter(new QubitInformationObject);

    $this->expectException(UnexpectedValueException::class);
    $writer->setModel('foo');
  }
}
