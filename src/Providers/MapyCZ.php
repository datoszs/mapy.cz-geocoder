<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder\Providers;

use DATOSCZ\MapyCzGeocoder\Exceptions\GeocodingException;
use DATOSCZ\MapyCzGeocoder\Exceptions\InvalidResultException;
use DATOSCZ\MapyCzGeocoder\Exceptions\MultipleResultsException;
use DATOSCZ\MapyCzGeocoder\Exceptions\NoResultException;
use DATOSCZ\MapyCzGeocoder\Exceptions\QueryException;
use DATOSCZ\MapyCzGeocoder\Exceptions\ReverseGeocodingException;
use DATOSCZ\MapyCzGeocoder\IGeocoder;
use DATOSCZ\MapyCzGeocoder\Utils\Coordinates;
use DATOSCZ\MapyCzGeocoder\Utils\HTTPXMLQuery;
use DATOSCZ\MapyCzGeocoder\Utils\Place;
use SimpleXMLElement;

class MapyCZ implements IGeocoder
{
	private const GEOCODE_URL = 'https://api.mapy.cz/geocode';
	private const GEOCODE_REVERSE_URL = 'https://api.mapy.cz/rgeocode';

	/** @var array */
	private $mapping;

	public function __construct()
	{
		$this->mapping = [
			'addr' => Place::ADDRESS,
			'stre' => Place::STREET,
			'quar' => Place::QURATER,
			'ward' => Place::WARD,
			'muni' => Place::MUNICIPALITY,
			'dist' => Place::DISTRICT,
			'regi' => Place::REGION,
			'coun' => Place::COUNTRY
		];
	}

	/**
	 * @inheritdoc
	 */
	public function geocode(string $value): Coordinates
	{
		try {
			$response = HTTPXMLQuery::performQuery(
				static::GEOCODE_URL, [
				'query' => $value,
			]
			);
		} catch (QueryException|InvalidResultException $exception) {
			throw new GeocodingException($exception->getMessage(), $exception->getCode(), $exception);
		}

		/** @var SimpleXMLElement $point */
		$point = $response->point;
		if ($point->children()->count() > 1) {
			throw new MultipleResultsException(sprintf('Found %d results expecting 1.', $point->children()->count()));
		}
		/** @var SimpleXMLElement $item */
		foreach ($point->children() as $item) {
			if (isset($item->attributes()->x) && isset($item->attributes()->y)) {
				return new Coordinates((float) $item->attributes()->y, (float) $item->attributes()->x);
			}
		}
		throw new NoResultException('No usable data for obtaining x and y of item');
	}

	/**
	 * @inheritdoc
	 */
	public function reverse(float $latitude, float $longitude): Place
	{
		try {
			$response = HTTPXMLQuery::performQuery(
				static::GEOCODE_REVERSE_URL, [
					'lon' => $longitude,
					'lat' => $latitude
				]
			);
		} catch (QueryException|InvalidResultException $exception) {
			throw new ReverseGeocodingException($exception->getMessage(), $exception->getCode(), $exception);
		}
		/** @var SimpleXMLElement $rgeocode */
		$rgeocode = $response;

		if ($rgeocode->children()->count() === 0) {
			throw new NoResultException('No items for this place.');
		}

		$label = (string) $rgeocode->attributes()->label;
		$point = new Coordinates($latitude, $longitude);
		$output = [];

		/** @var SimpleXMLElement $item */
		foreach ($rgeocode->children() as $item) {
			$output[$this->mapping[(string) $item->attributes()->type]] = (string) $item->attributes()->name;
		}
		return new Place($point, $label, $output);
	}

	/**
	 * @inheritdoc
	 */
	public function reverseCoordinates(Coordinates $coordinates): Place
	{
		return $this->reverse($coordinates->getLatitude(), $coordinates->getLongitude());
	}
}
