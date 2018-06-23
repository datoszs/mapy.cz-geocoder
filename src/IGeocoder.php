<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder;

use DATOSCZ\MapyCzGeocoder\Exceptions\MultipleResultsException;
use DATOSCZ\MapyCzGeocoder\Exceptions\NoResultException;
use DATOSCZ\MapyCzGeocoder\Utils\Coordinates;
use DATOSCZ\MapyCzGeocoder\Utils\Place;
use DATOSCZ\MapyCzGeocoder\Exceptions\GeocodingException;
use DATOSCZ\MapyCzGeocoder\Exceptions\ReverseGeocodingException;

interface IGeocoder
{
	/**
	 * Geocodes a given address or name.
	 *
	 * @param string $value
	 *
	 * @return Coordinates
	 * @throws GeocodingException
	 * @throws MultipleResultsException when more results are obtained for given address
	 * @throws NoResultException when no results are obtained or coordinates could not been parsed
	 */
	public function geocode(string $value): Coordinates;
	/**
	 * Reverses geocode given coordinates (from given latitude and longitude).
	 *
	 * @param float $latitude
	 * @param float $longitude
	 *
	 * @return Place
	 * @throws ReverseGeocodingException
	 * @throws NoResultException when no details about place are present
	 */
	public function reverse(float $latitude, float $longitude): Place;


	/**
	 * Reverse geocode given coordinates (from given coordinates object)
	 * @param Coordinates $coordinates
	 *
	 * @return Place
	 * @throws ReverseGeocodingException
	 * @throws NoResultException when no details about place are present
	 */
	public function reverseCoordinates(Coordinates $coordinates): Place;
}
