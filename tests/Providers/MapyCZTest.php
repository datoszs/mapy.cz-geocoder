<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder\Tests\Providers;

use DATOSCZ\MapyCzGeocoder\Exceptions\MultipleResultsException;
use DATOSCZ\MapyCzGeocoder\Exceptions\NoResultException;
use DATOSCZ\MapyCzGeocoder\Providers\MapyCZ;
use DATOSCZ\MapyCzGeocoder\Utils\Coordinates;
use PHPUnit\Framework\TestCase;

class MapyCZTest extends TestCase
{

	public function testGeocode()
	{
		$geocoder = new MapyCZ();

		// Valid address
		$coordinates = $geocoder->geocode('Moravské náměstí 3, Brno, 602 00');
		$this->assertEquals($coordinates->getLongitude(), '16.6082');
		$this->assertEquals($coordinates->getLatitude(), '49.1974');

		// Multiple address
		try {
			$geocoder->geocode('Nad Vodojemem');
			$this->fail('MultipleResultsException is expected');
		} catch (MultipleResultsException $exception) {
			$this->assertTrue(true); // exception is expected
		}

		// Not existing address
		try {
			$geocoder->geocode('Terryho Pratchetta');
			$this->fail('NoResultException is expected');
		} catch (NoResultException $exception) {
			$this->assertTrue(true); // exception is expected
		}

		// Empty address
		try {
			$geocoder->geocode('');
			$this->fail('NoResultException is expected');
		} catch (NoResultException $exception) {
			$this->assertTrue(true); // exception is expected
		}

	}

	public function testReverse()
	{
		$geocoder = new MapyCZ();
		$output = $geocoder->reverse(50.131282, 14.418415);
		$this->assertEquals($output->getCoordinates()->getLatitude(), 50.131282);
		$this->assertEquals($output->getCoordinates()->getLongitude(), 14.418415);
		$this->assertEquals($output->getLabel(), 'ulice Lodžská, Praha, okres Hlavní město Praha');

		$geocoder = new MapyCZ();
		$output = $geocoder->reverse(49.1974, 16.6082);
		$this->assertEquals($output->getCoordinates()->getLatitude(), 49.1974);
		$this->assertEquals($output->getCoordinates()->getLongitude(), 16.6082);
		$this->assertEquals($output->getLabel(), 'Moravské náměstí 127/3, Brno, 602 00, okres Brno-město');
	}

	public function testReverseCoordinates()
	{
		$geocoder = new MapyCZ();
		$output = $geocoder->reverseCoordinates(new Coordinates(49.1974, 16.6082));
		$this->assertEquals($output->getCoordinates()->getLatitude(), 49.1974);
		$this->assertEquals($output->getCoordinates()->getLongitude(), 16.6082);
		$this->assertEquals($output->getLabel(), 'Moravské náměstí 127/3, Brno, 602 00, okres Brno-město');
	}
}
