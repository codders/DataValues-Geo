<?php

namespace Tests\DataValues\Geo\Formatters;

use DataValues\Geo\Formatters\GeoCoordinateFormatter;
use DataValues\Geo\Parsers\GeoCoordinateParser;
use DataValues\Geo\Values\LatLongValue;
use DataValues\StringValue;
use ValueFormatters\FormatterOptions;

/**
 * @covers DataValues\Geo\Formatters\GeoCoordinateFormatter
 *
 * @group ValueFormatters
 * @group DataValueExtensions
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Addshore
 * @author Daniel Kinzler
 */
class GeoCoordinateFormatterTest extends \PHPUnit_Framework_TestCase {

	public function floatNotationProvider() {
		return array(
			'0, degree' => array(
				new LatLongValue( 0, 0 ),
				1,
				'0, 0'
			),
			'negative zero' => array(
				new LatLongValue( -0.25, 0.25 ),
				1,
				'0, 0'
			),
			'signed, minute' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 60,
				'-55.75, 37.25'
			),
			'signed, degree' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1,
				'-56, 37'
			),
			'three degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				3,
				'-57, 36'
			),
			'seven degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				7,
				'-56, 35'
			),
			'ten degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10,
				'-60, 40'
			),
			'rounding degrees down' => array(
				new LatLongValue( -14.9, 14.9 ),
				10,
				'-10, 10'
			),
			'rounding degrees up' => array(
				new LatLongValue( -15, 15 ),
				10,
				'-20, 20'
			),
			'rounding fractions down' => array(
				new LatLongValue( -0.049, 0.049 ),
				0.1,
				'0, 0'
			),
			'rounding fractions up' => array(
				new LatLongValue( -0.05, 0.05 ),
				0.1,
				'-0.1, 0.1'
			),
			'precision option must support strings' => array(
				new LatLongValue( -0.05, 0.05 ),
				'0.1',
				'-0.1, 0.1'
			),
		);
	}

	/**
	 * @param string $format One of the GeoCoordinateFormatter::TYPE_… constants
	 * @param float|int $precision
	 *
	 * @return FormatterOptions
	 */
	private function makeOptions( $format, $precision ) {
		$options = new FormatterOptions();
		$options->setOption( GeoCoordinateFormatter::OPT_FORMAT, $format );
		$options->setOption( GeoCoordinateFormatter::OPT_DIRECTIONAL, false );
		$options->setOption( GeoCoordinateFormatter::OPT_PRECISION, $precision );

		return $options;
	}

	/**
	 * @dataProvider floatNotationProvider
	 */
	public function testFloatNotationFormatting( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_FLOAT, $precision );
		$this->assertFormatsCorrectly( $latLong, $options, $expected );
	}

	/**
	 * @dataProvider floatNotationProvider
	 */
	public function testFloatNotationRoundTrip( LatLongValue $value, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_FLOAT, $precision );
		$this->assertRoundTrip( $value, $options );
	}

	public function decimalDegreeNotationProvider() {
		return array(
			'0, degree' => array(
				new LatLongValue( 0, 0 ),
				1,
				'0°, 0°'
			),
			'negative zero' => array(
				new LatLongValue( -0.25, 0.25 ),
				1,
				'0°, 0°'
			),
			'signed, minute' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 60,
				'-55.75°, 37.25°'
			),
			'signed, degree' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1,
				'-56°, 37°'
			),
			'three degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				3,
				'-57°, 36°'
			),
			'seven degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				7,
				'-56°, 35°'
			),
			'ten degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10,
				'-60°, 40°'
			),
			'rounding degrees down' => array(
				new LatLongValue( -14.9, 14.9 ),
				10,
				'-10°, 10°'
			),
			'rounding degrees up' => array(
				new LatLongValue( -15, 15 ),
				10,
				'-20°, 20°'
			),
			'rounding fractions down' => array(
				new LatLongValue( -0.049, 0.049 ),
				0.1,
				'0.0°, 0.0°'
			),
			'rounding fractions up' => array(
				new LatLongValue( -0.05, 0.05 ),
				0.1,
				'-0.1°, 0.1°'
			),
			'precision option must support strings' => array(
				new LatLongValue( -0.05, 0.05 ),
				'0.1',
				'-0.1°, 0.1°'
			),
		);
	}

	/**
	 * @dataProvider decimalDegreeNotationProvider
	 */
	public function testDecimalDegreeNotationFormatting( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DD, $precision );
		$this->assertFormatsCorrectly( $latLong, $options, $expected );
	}

	/**
	 * @dataProvider decimalDegreeNotationProvider
	 */
	public function testDecimalDegreeNotationRoundTrip( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DD, $precision );
		$this->assertRoundTrip( $latLong, $options );
	}

	public function decimalMinuteNotationProvider() {
		return array(
			'0, degree' => array(
				new LatLongValue( 0, 0 ),
				1,
				'0°, 0°'
			),
			'0, minute' => array(
				new LatLongValue( 0, 0 ),
				1 / 60,
				'0° 0\', 0° 0\''
			),
			'0, second' => array(
				new LatLongValue( 0, 0 ),
				1 / 3600,
				'0° 0.00\', 0° 0.00\''
			),
			'negative zero' => array(
				new LatLongValue( -1 / 128, 1 / 128 ),
				1 / 60,
				'0° 0\', 0° 0\''
			),
			'negative, not zero' => array(
				new LatLongValue( -0.25, 0.25 ),
				1 / 60,
				'-0° 15\', 0° 15\''
			),
			'second' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 3600,
				'-55° 45.35\', 37° 15.38\''
			),
			'minute' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 60,
				'-55° 45\', 37° 15\''
			),
			'ten minutes' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10 / 60,
				'-55° 50\', 37° 20\''
			),
			'fifty minutes' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				50 / 60,
				'-55° 50\', 37° 30\''
			),
			'degree' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1,
				'-56°, 37°'
			),
			'ten degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10,
				'-60°, 40°'
			),
			'rounding minutes down' => array(
				new LatLongValue( -14.9 / 60, 14.9 / 60 ),
				10 / 60,
				'-0° 10\', 0° 10\''
			),
			'rounding minutes up' => array(
				new LatLongValue( -15 / 60, 15 / 60 ),
				10 / 60,
				'-0° 20\', 0° 20\''
			),
			'rounding fractions down' => array(
				new LatLongValue( -0.049 / 60, 0.049 / 60 ),
				0.1 / 60,
				'0° 0.0\', 0° 0.0\''
			),
			'rounding fractions up' => array(
				new LatLongValue( -0.05 / 60, 0.05 / 60 ),
				0.1 / 60,
				'-0° 0.1\', 0° 0.1\''
			),
			'precision option must support strings' => array(
				new LatLongValue( -0.05, 0.05 ),
				'0.1',
				'-0° 6\', 0° 6\''
			),
		);
	}

	/**
	 * @dataProvider decimalMinuteNotationProvider
	 */
	public function testDecimalMinuteNotationFormatting( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DM, $precision );
		$this->assertFormatsCorrectly( $latLong, $options, $expected );
	}

	/**
	 * @dataProvider decimalMinuteNotationProvider
	 */
	public function testDecimalMinuteNotationRoundTrip( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DM, $precision );
		$this->assertRoundTrip( $latLong, $options );
	}

	public function decimalMinuteSecondNotationProvider() {
		return array(
			'0, degree' => array(
				new LatLongValue( 0, 0 ),
				1,
				'0°, 0°'
			),
			'0, minute' => array(
				new LatLongValue( 0, 0 ),
				1 / 60,
				'0° 0\', 0° 0\''
			),
			'0, second' => array(
				new LatLongValue( 0, 0 ),
				1 / 3600,
				'0° 0\' 0", 0° 0\' 0"'
			),
			'negative zero' => array(
				new LatLongValue( -1 / 8192, 1 / 8192 ),
				1 / 3600,
				'0° 0\' 0", 0° 0\' 0"'
			),
			'negative, not zero' => array(
				new LatLongValue( -1 / 4096, 1 / 4096 ),
				1 / 7200,
				'-0° 0\' 1.0", 0° 0\' 1.0"'
			),
			'second' => array(
				new LatLongValue( -55.755786, 37.25 ),
				1 / 3600,
				'-55° 45\' 21", 37° 15\' 0"'
			),
			'second/100' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 360000,
				'-55° 45\' 20.83", 37° 15\' 22.79"'
			),
			'ten seconds' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10 / 3600,
				'-55° 45\' 20", 37° 15\' 20"'
			),
			'fifty seconds' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				50 / 3600,
				'-55° 45\' 0", 37° 15\' 0"'
			),
			'minute' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1 / 60,
				'-55° 45\', 37° 15\''
			),
			'degree' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				1,
				'-56°, 37°'
			),
			'degree/100, case A' => array(
				new LatLongValue( 52.01, 10.01 ),
				0.01,
				'52° 0\' 36", 10° 0\' 36"'
			),
			'degree/100, case B' => array(
				new LatLongValue( 52.02, 10.02 ),
				0.01,
				'52° 1\' 12", 10° 1\' 12"'
			),
			'degree/1000' => array(
				new LatLongValue( 52.4, 6.7667 ),
				0.001,
				'52° 24\' 0", 6° 46\' 1"'
			),
			'ten degrees' => array(
				new LatLongValue( -55.755786, 37.25633 ),
				10,
				'-60°, 40°'
			),
			'rounding seconds down' => array(
				new LatLongValue( -14.9 / 3600, 14.9 / 3600 ),
				10 / 3600,
				'-0° 0\' 10", 0° 0\' 10"'
			),
			'rounding seconds up' => array(
				new LatLongValue( -15 / 3600, 15 / 3600 ),
				10 / 3600,
				'-0° 0\' 20", 0° 0\' 20"'
			),
			'rounding fractions down' => array(
				new LatLongValue( -0.049 / 3600, 0.049 / 3600 ),
				0.1 / 3600,
				'0° 0\' 0.0", 0° 0\' 0.0"'
			),
			'rounding fractions up' => array(
				new LatLongValue( -0.05 / 3600, 0.05 / 3600 ),
				0.1 / 3600,
				'-0° 0\' 0.1", 0° 0\' 0.1"'
			),
			'precision option must support strings' => array(
				new LatLongValue( -0.05, 0.05 ),
				'0.1',
				'-0° 6\', 0° 6\''
			),
			'Bug T150085 with 1 second precision' => array(
				new LatLongValue( 42.1206, 2.76944 ),
				1 / 3600,
				'42° 7\' 14", 2° 46\' 10"'
			),
			'Bug T150085 with 1 minute precision' => array(
				new LatLongValue( 42.1206, 2.76944 ),
				1 / 60,
				'42° 7\', 2° 46\''
			),
			'Bug T150085 with ~0.7 minute precision' => array(
				new LatLongValue( 42.1206, 2.76944 ),
				0.012111004438894,
				'42° 7\' 19", 2° 46\' 24"'
			),
		);
	}

	/**
	 * @dataProvider decimalMinuteSecondNotationProvider
	 */
	public function testDecimalMinuteSecondNotationFormatting( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DMS, $precision );
		$this->assertFormatsCorrectly( $latLong, $options, $expected );
	}

	/**
	 * @dataProvider decimalMinuteSecondNotationProvider
	 */
	public function testDecimalMinuteSecondNotationRoundTrip( LatLongValue $latLong, $precision, $expected ) {
		$options = $this->makeOptions( GeoCoordinateFormatter::TYPE_DMS, $precision );
		$this->assertRoundTrip( $latLong, $options );
	}

	/**
	 * @param LatLongValue $latLong
	 * @param FormatterOptions $options
	 * @param string $expected
	 */
	private function assertFormatsCorrectly( LatLongValue $latLong, FormatterOptions $options, $expected ) {
		$formatter = new GeoCoordinateFormatter( $options );

		$this->assertSame(
			$expected,
			$formatter->format( $latLong ),
			'format()'
		);

		$precision = $options->getOption( GeoCoordinateFormatter::OPT_PRECISION );
		$this->assertSame(
			$expected,
			$formatter->formatLatLongValue( $latLong, $precision ),
			'formatLatLongValue()'
		);
	}

	private function assertRoundTrip( LatLongValue $value, FormatterOptions $options ) {
		$formatter = new GeoCoordinateFormatter( $options );
		$parser = new GeoCoordinateParser();

		$formatted = $formatter->format( $value );
		$parsed = $parser->parse( $formatted );

		// NOTE: $parsed may be != $coord, because of rounding, so we can't compare directly.
		$formattedParsed = $formatter->format( $parsed );

		$this->assertSame( $formatted, $formattedParsed );
	}

	public function testDirectionalOptionGetsAppliedForDecimalMinutes() {
		$coordinates = array(
			'55° 0\' N, 37° 0\' E' => array( 55, 37 ),
			'55° 30\' N, 37° 30\' W' => array( 55.5, -37.5 ),
			'55° 30\' S, 37° 30\' E' => array( -55.5, 37.5 ),
			'55° 30\' S, 37° 30\' W' => array( -55.5, -37.5 ),
			'0° 0\' N, 0° 0\' E' => array( 0, 0 ),
		);

		$this->assertIsDirectionalFormatMap( $coordinates, GeoCoordinateFormatter::TYPE_DM );
	}

	/**
	 * @param array[] $coordinates
	 * @param string $format One of the GeoCoordinateFormatter::TYPE_… constants
	 */
	private function assertIsDirectionalFormatMap( array $coordinates, $format ) {
		foreach ( $coordinates as $expected => $arguments ) {
			$options = new FormatterOptions();
			$options->setOption( GeoCoordinateFormatter::OPT_FORMAT, $format );
			$options->setOption( GeoCoordinateFormatter::OPT_DIRECTIONAL, true );
			$options->setOption( GeoCoordinateFormatter::OPT_PRECISION, 1 / 60 );

			$this->assertFormatsCorrectly(
				new LatLongValue( $arguments[0], $arguments[1] ),
				$options,
				$expected
			);
		}
	}

	public function testDirectionalOptionGetsAppliedForFloats() {
		$coordinates = array(
			'55.75 N, 37.25 W' => array( 55.755786, -37.25633 ),
			'55.75 S, 37.25 E' => array( -55.755786, 37.25633 ),
			'55 S, 37.25 W' => array( -55, -37.25633 ),
			'5.5 N, 37 E' => array( 5.5, 37 ),
			'0 N, 0 E' => array( 0, 0 ),
		);

		$this->assertIsDirectionalFormatMap( $coordinates, GeoCoordinateFormatter::TYPE_FLOAT );
	}

	private function provideSpacingLevelOptions() {
		return array(
			'none' => array(),
			'latlong' => array( GeoCoordinateFormatter::OPT_SPACE_LATLONG ),
			'direction' => array( GeoCoordinateFormatter::OPT_SPACE_DIRECTION ),
			'coordparts' => array( GeoCoordinateFormatter::OPT_SPACE_COORDPARTS ),
			'latlong_direction' => array(
				GeoCoordinateFormatter::OPT_SPACE_LATLONG,
				GeoCoordinateFormatter::OPT_SPACE_DIRECTION
			),
			'all' => array(
				GeoCoordinateFormatter::OPT_SPACE_LATLONG,
				GeoCoordinateFormatter::OPT_SPACE_DIRECTION,
				GeoCoordinateFormatter::OPT_SPACE_COORDPARTS,
			),
		);
	}

	public function testSpacingOptionGetsAppliedForDecimalMinutes() {
		$coordinates = array(
			'none' => array(
				'55°0\'N,37°0\'E' => array( 55, 37 ),
				'55°30\'N,37°30\'W' => array( 55.5, -37.5 ),
				'0°0\'N,0°0\'E' => array( 0, 0 ),
			),
			'latlong' => array(
				'55°0\'N, 37°0\'E' => array( 55, 37 ),
				'55°30\'N, 37°30\'W' => array( 55.5, -37.5 ),
				'0°0\'N, 0°0\'E' => array( 0, 0 ),
			),
			'direction' => array(
				'55°0\' N,37°0\' E' => array( 55, 37 ),
				'55°30\' N,37°30\' W' => array( 55.5, -37.5 ),
				'0°0\' N,0°0\' E' => array( 0, 0 ),
			),
			'coordparts' => array(
				'55° 0\'N,37° 0\'E' => array( 55, 37 ),
				'55° 30\'N,37° 30\'W' => array( 55.5, -37.5 ),
				'0° 0\'N,0° 0\'E' => array( 0, 0 ),
			),
			'latlong_direction' => array(
				'55°0\' N, 37°0\' E' => array( 55, 37 ),
				'55°30\' N, 37°30\' W' => array( 55.5, -37.5 ),
				'0°0\' N, 0°0\' E' => array( 0, 0 ),
			),
		);

		$this->assertSpacingCorrect( $coordinates, GeoCoordinateFormatter::TYPE_DM );
	}

	/**
	 * @param array[] $coordSets
	 * @param string $format One of the GeoCoordinateFormatter::TYPE_… constants
	 */
	private function assertSpacingCorrect( array $coordSets, $format ) {
		$spacingLevelOptions = $this->provideSpacingLevelOptions();
		foreach ( $coordSets as $spacingKey => $coordinates ) {
			foreach ( $coordinates as $expected => $arguments ) {
				$options = new FormatterOptions();
				$options->setOption( GeoCoordinateFormatter::OPT_FORMAT, $format );
				$options->setOption( GeoCoordinateFormatter::OPT_DIRECTIONAL, true );
				$options->setOption( GeoCoordinateFormatter::OPT_PRECISION, 1 / 60 );
				$options->setOption( GeoCoordinateFormatter::OPT_SPACING_LEVEL, $spacingLevelOptions[$spacingKey] );

				$this->assertFormatsCorrectly(
					new LatLongValue( $arguments[0], $arguments[1] ),
					$options,
					$expected
				);
			}
		}
	}

	public function testSpacingOptionGetsAppliedForFloats() {
		$coordinates = array(
			'none' => array(
				'55.75N,37.25W' => array( 55.755786, -37.25633 ),
				'0N,0E' => array( 0, 0 ),
			),
			'latlong' => array(
				'55.75N, 37.25W' => array( 55.755786, -37.25633 ),
				'0N, 0E' => array( 0, 0 ),
			),
			'direction' => array(
				'55.75 N,37.25 W' => array( 55.755786, -37.25633 ),
				'0 N,0 E' => array( 0, 0 ),
			),
			'coordparts' => array(
				'55.75N,37.25W' => array( 55.755786, -37.25633 ),
				'0N,0E' => array( 0, 0 ),
			),
			'latlong_direction' => array(
				'55.75 N, 37.25 W' => array( 55.755786, -37.25633 ),
				'0 N, 0 E' => array( 0, 0 ),
			),
			'all' => array(
				'55.75 N, 37.25 W' => array( 55.755786, -37.25633 ),
				'0 N, 0 E' => array( 0, 0 ),
			),
		);

		$this->assertSpacingCorrect( $coordinates, GeoCoordinateFormatter::TYPE_FLOAT );
	}

	public function testWrongType() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$formatter = new GeoCoordinateFormatter( new FormatterOptions() );

		$formatter->format( new StringValue( 'Evil' ) );
	}

	public function testGivenInvalidFormattingOption_formatThrowsException() {
		$options = new FormatterOptions();
		$options->setOption( GeoCoordinateFormatter::OPT_FORMAT, 'not a format' );
		$formatter = new GeoCoordinateFormatter( $options );

		$this->setExpectedException( 'InvalidArgumentException' );
		$formatter->format( new LatLongValue( 0, 0 ) );
	}

	/**
	 * @dataProvider invalidPrecisionProvider
	 */
	public function testFormatWithInvalidPrecision_fallsBackToDefaultPrecision( $precision ) {
		$options = new FormatterOptions();
		$options->setOption( GeoCoordinateFormatter::OPT_PRECISION, $precision );
		$formatter = new GeoCoordinateFormatter( $options );

		$formatted = $formatter->format( new LatLongValue( 1.2, 3.4 ) );
		$this->assertSame( '1.2, 3.4', $formatted );
	}

	/**
	 * @dataProvider invalidPrecisionProvider
	 */
	public function testFormatLatLongValueWithInvalidPrecision_fallsBackToDefaultPrecision( $precision ) {
		$formatter = new GeoCoordinateFormatter( new FormatterOptions() );

		$formatted = $formatter->formatLatLongValue( new LatLongValue( 1.2, 3.4 ), $precision );
		$this->assertSame( '1.2, 3.4', $formatted );
	}

	public function invalidPrecisionProvider() {
		return array(
			array( null ),
			array( '' ),
			array( 0 ),
			array( -1 ),
			array( NAN ),
			array( INF ),
		);
	}

}
