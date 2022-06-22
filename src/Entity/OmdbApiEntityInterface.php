<?php

namespace Drupal\omdb_api\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\RevisionLogInterface;

/**
 * Provides an interface defining an omdb api entity type.
 */
interface OmdbApiEntityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface, RevisionLogInterface {

  /**
   * Gets the IMDB Name of OMDB API Entity.
   *
   * @return string
   *   Returns IMDB Name of the OMDB API.
   */
  public function getImdbTitle();

  /**
   * Sets the IMDB Name of OMDB API Entity.
   *
   * @param string $imdb_title
   *   The IMDB Name of the OMDB API.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setImdbTitle($imdb_title);

  /**
   * Gets the IMDB Id of OMDB API Entity.
   *
   * @return string
   *   Returns IMDB Id of the OMDB API.
   */
  public function getImdbId();

  /**
   * Sets the IMDB Id of OMDB API Entity.
   *
   * @param string $imdb_id
   *   The IMDB Id of the OMDB API.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setImdbId($imdb_id);

  /**
   * Gets the OMDB API creation timestamp.
   *
   * @return int
   *   Creation timestamp of the OMDB API.
   */
  public function getCreatedTime();

  /**
   * Sets the OMDB API creation timestamp.
   *
   * @param int $timestamp
   *   The OMDB API creation timestamp.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the OMDB API published status indicator.
   *
   * Unpublished OMDB API are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the OMDB API is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a OMDB API.
   *
   * @param bool $published
   *   TRUE to set this OMDB API to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API entity.
   */
  public function setPublished($published);

  /**
   * Gets the OMDB API revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the OMDB API revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the OMDB API revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the OMDB API revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setRevisionUserId($uid);

  /**
   * Gets The Year that the Movie or Series was Released.
   *
   * @return string
   *   The Year that the Movie or Series was Released.
   */
  public function getReleasedYear();

  /**
   * Sets The Year that the Movie or Series was Released.
   *
   * @param string $released_year
   *   The Year that the Movie or Series was Released.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setReleasedYear($released_year);

  /**
   * Gets The Date that the Movie or Series was Released.
   *
   * @return string
   *   The Date that the Movie or Series was Released.
   */
  public function getReleasedDate();

  /**
   * Sets The Date that the Movie or Series was Released.
   *
   * @param string $released_date
   *   The Date that the Movie or Series was Released.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setReleasedDate($released_date);

  /**
   * Gets The Viewer Rating of the Movie or Series.
   *
   * @return string
   *   The Viewer Rating of the Movie or Series.
   */
  public function getViewerRating();

  /**
   * Sets The Viewer Rating of the Movie or Series.
   *
   * @param string $viewer_rating
   *   The Viewer Rating of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setViewerRating($viewer_rating);

  /**
   * Gets The Runtime of the Movie or Series.
   *
   * @return string
   *   The Runtime of the Movie or Series.
   */
  public function getRuntime();

  /**
   * Sets The Runtime of the Movie or Series.
   *
   * @param string $runtime
   *   The Runtime of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setRuntime($runtime);

  /**
   * Gets The Genre of the Movie or Series.
   *
   * @return string
   *   The Genre of the Movie or Series.
   */
  public function getGenre();

  /**
   * Sets The Genre of the Movie or Series.
   *
   * @param string $genre
   *   The Genre of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setGenre($genre);

  /**
   * Gets The Director of the Movie or Series.
   *
   * @return string
   *   The Director of the Movie or Series.
   */
  public function getDirector();

  /**
   * Sets The Genre of the Movie or Series.
   *
   * @param string $director
   *   The Director of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setDirector($director);

  /**
   * Gets The Writer of the Movie or Series.
   *
   * @return string
   *   The writer of the Movie or Series.
   */
  public function getWriter();

  /**
   * Sets The Writer of the Movie or Series.
   *
   * @param string $writer
   *   The Writer of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setWriter($writer);

  /**
   * Gets The Actors of the Movie or Series.
   *
   * @return string
   *   The Actors of the Movie or Series.
   */
  public function getActors();

  /**
   * Sets The Actors of the Movie or Series.
   *
   * @param string $actors
   *   The Actors of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setActors($actors);

  /**
   * Gets The plot of the Movie or Series.
   *
   * @return string
   *   The plot of the Movie or Series.
   */
  public function getPlot();

  /**
   * Sets The plot of the Movie or Series.
   *
   * @param string $plot
   *   The plot of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setPlot($plot);

  /**
   * Gets The language of the Movie or Series.
   *
   * @return string
   *   The language of the Movie or Series.
   */
  public function getLanguage();

  /**
   * Sets The language of the Movie or Series.
   *
   * @param string $language
   *   The language of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setLanguage($language);

  /**
   * Gets The country of the Movie or Series.
   *
   * @return string
   *   The country of the Movie or Series.
   */
  public function getCountry();

  /**
   * Sets The country of the Movie or Series.
   *
   * @param string $country
   *   The country of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setCountry($country);

  /**
   * Gets The country of the Movie or Series.
   *
   * @return string
   *   The country of the Movie or Series.
   */
  public function getAwards();

  /**
   * Sets The awards of the Movie or Series.
   *
   * @param string $awards
   *   The awards of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setAwards($awards);

  /**
   * Gets The poster of the Movie or Series.
   *
   * @return string
   *   The poster of the Movie or Series.
   */
  public function getPoster();

  /**
   * Sets The poster of the Movie or Series.
   *
   * @param string $poster
   *   The poster of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setPoster($poster);

  /**
   * Gets The ratings of the Movie or Series.
   *
   * @return string
   *   The ratings of the Movie or Series.
   */
  public function getRatings();

  /**
   * Sets The ratings of the Movie or Series.
   *
   * @param array|string $ratings
   *   The ratings of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setRatings($ratings);

  /**
   * Gets The metascore of the Movie or Series.
   *
   * @return string
   *   The metascore of the Movie or Series.
   */
  public function getMetascore();

  /**
   * Sets The metascore of the Movie or Series.
   *
   * @param string $metascore
   *   The metascore of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setMetascore($metascore);

  /**
   * Gets The imdb_rating of the Movie or Series.
   *
   * @return string
   *   The imdb_rating of the Movie or Series.
   */
  public function getimdbRating();

  /**
   * Sets The imdb_rating of the Movie or Series.
   *
   * @param string $imdb_rating
   *   The imdb_rating of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setimdbRating($imdb_rating);

  /**
   * Gets The imdb_votes of the Movie or Series.
   *
   * @return string
   *   The imdb_votes of the Movie or Series.
   */
  public function getimdbVotes();

  /**
   * Sets The imdb_votes of the Movie or Series.
   *
   * @param string $imdb_votes
   *   The imdb_votes of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setimdbVotes($imdb_votes);

  /**
   * Gets The dvd_released_year of the Movie or Series.
   *
   * @return string
   *   The dvd_released_year of the Movie or Series.
   */
  public function getDvdReleasedYear();

  /**
   * Sets The dvd_released_year of the Movie or Series.
   *
   * @param string $dvd_released_year
   *   The dvd_released_year of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setDvdReleasedYear($dvd_released_year);

  /**
   * Gets The box_office_collections of the Movie or Series.
   *
   * @return string
   *   The box_office_collections of the Movie or Series.
   */
  public function getBoxOfficeCollections();

  /**
   * Sets The box_office_collections of the Movie or Series.
   *
   * @param string $box_office_collections
   *   The box_office_collections of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setBoxOfficeCollections($box_office_collections);

  /**
   * Gets The production of the Movie or Series.
   *
   * @return string
   *   The production of the Movie or Series.
   */
  public function getProductionHouse();

  /**
   * Sets The production of the Movie or Series.
   *
   * @param string $production
   *   The production of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setProductionHouse($production);

  /**
   * Gets The website of the Movie or Series.
   *
   * @return string
   *   The website of the Movie or Series.
   */
  public function getWebsite();

  /**
   * Sets The country of the Movie or Series.
   *
   * @param string $website
   *   The website of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setWebsite($website);

  /**
   * Gets The api_response status of the Movie or Series.
   *
   * @return string
   *   The api_response status of the Movie or Series.
   */
  public function getApiResponseStatus();

  /**
   * Sets The api_response status of the Movie or Series.
   *
   * @param string $api_response
   *   The api_response status of the Movie or Series.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setApiResponseStatus($api_response);

  /**
   * Gets Data Refreshed from API.
   *
   * @return string
   *   The api_response status of the Movie or Series.
   */
  public function isRefreshData();

  /**
   * Sets The refresh_data status of the Movie or Series.
   *
   * @param bool $refreshed
   *   Option to Refresh Data.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The called OMDB API Entity.
   */
  public function setRefreshData($refreshed);

}
