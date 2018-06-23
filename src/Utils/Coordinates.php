<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder\Utils;

/**
 * Immutable representation of coordinates.
 */
class Coordinates
{
	/** @var float */
	private $latitude;

	/** @var float */
	private $longitude;

	public function __construct(float $latitude, float $longitude)
	{
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	public function getLatitude(): float
	{
		return $this->latitude;
	}

	public function getLongitude(): float
	{
		return $this->longitude;
	}

	public function __toString(): string
	{
		return sprintf('[%f, %f]', $this->latitude, $this->longitude);
	}
}
