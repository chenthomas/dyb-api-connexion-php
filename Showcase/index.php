<?php
    require_once('./ShowcaseApiWrapper.php');
    require_once('./config.php');

    const USERS_REQUEST = 0;
    const USERS_SEARCH_REQUEST = 16;
    const USER_REQUEST = 1;
    const USER_TOKEN_REQUEST = 2;
    const USER_TAG_REQUEST = 3;
    const USER_TAG_ASSOCIATION_REQUEST = 4;
    const USER_TAG_DISSOCIATION_REQUEST = 5;
    const USER_COMMENTS = 19;
    const TAGS_REQUEST = 6;
    const TAG_REQUEST = 7;
    const TAG_USERS_REQUEST = 8;
    const DYB_USER_REQUEST = 9;
    const DYB_USERS_LASTUPDATE = 10;
    const DYB_USERS_CREATED = 11;
    const DYB_USERS_LASTDELETE = 12;
    const DYB_CV_REQUEST = 13;
    const DYB_EMPLOYMENT_PREFERENCES_REQUEST = 14;
    const DYB_SEARCH_REQUEST = 15;
    const DYB_METADATA_REQUEST = 17;
    const DYB_CV_DISPLAY_CONFIG = 18;

    $requestTranslation = array(
        USERS_REQUEST => "Users",
        USERS_SEARCH_REQUEST => "Search among users",
        USER_REQUEST => "One user",
        USER_TOKEN_REQUEST => "User token",
        USER_TAG_REQUEST => "User tags",
        USER_TAG_ASSOCIATION_REQUEST => "Associate tag to a user",
        USER_TAG_DISSOCIATION_REQUEST => "Dissociate tag to a user",
        USER_COMMENTS => "Get comments",
        TAGS_REQUEST => "Tags",
        TAG_REQUEST => "One Tag",
        TAG_USERS_REQUEST =>  "Users associated to a tag",
        DYB_USER_REQUEST =>  "User's DoYouBuzz profile",
        DYB_USERS_LASTUPDATE => "Last udpated users since a specific date",
        DYB_USERS_CREATED => "Created users since a specific date",
        DYB_USERS_LASTDELETE => "Last deleted users since a specific date",
        DYB_CV_REQUEST =>  "Users's DoYouBuzz resume",
        DYB_EMPLOYMENT_PREFERENCES_REQUEST => "User's DoYouBuzz employment preferences",
        DYB_SEARCH_REQUEST => "Search",
        DYB_METADATA_REQUEST => "Update metadata for user",
        DYB_CV_DISPLAY_CONFIG => "Get the user's display configuration for his resume (color, design id ...)",
        DYB_CV_LIST => "Get Resumes list (filtered or not)",
        DYB_SET_MANAGER => "Set user's manager"
    );

    // Configuration
    $apikey = $config['apikey'];
    $apisecret = $config['apisecret'];
    $userId = $config['userId'];
    $cvId = $config['cvId'];
    $tagId = $config['tagId'];
    $searchTerm = $config['searchTerm'];
    $managerId = $config['managerId'];

    $isHome = !isset($_GET['request']);

    // if the requested url has no params, just display the home page
    if ($isHome) { return displayHomePage($requestTranslation); }

    /* Change this with the constants above to test the possible requests to the Showcase API */
    $request = $_GET['request'];

    $shwApi = new ShowcaseApiWrapper($apikey, $apisecret);
    $data = null;
    switch($request) {
        case USERS_REQUEST:
            $data = $shwApi->doRequest("users");
            break;
        case USERS_SEARCH_REQUEST:
            $data = $shwApi->doRequest("users/search", array('term' => $searchTerm));
            break;
        case USER_REQUEST:
            $data = $shwApi->doRequest("users/". $userId, array('isIdOrigin' => 1));
            break;
        case USER_TOKEN_REQUEST:
            $data = $shwApi->doRequest("users/". $userId ."/token", array('isIdOrigin' => 1));
            break;
        case USER_COMMENTS:
            $filters = [ "filters" => [
                [ "type" => "created", "value" => "2017-02-02", "comparator" => "<" ],
                [ "type" => "updated", "value" => "2017-02-02", "comparator" => "<" ],
                [ "type" => "user", "value" => $userId ]
            ], "sort" => [
                [ "field" => "created", "order" => "ASC"]
            ]];
            $filtersJson = json_encode($filters);
            $data = $shwApi->doRequest("comments", [], $filtersJson);
            break;
        case USER_TAG_REQUEST:
            $data = $shwApi->doRequest("users/" . $userId . "/tags", array('isIdOrigin' => 1));
            break;
        case "DYB_SET_MANAGER":
            $data = $shwApi->doRequest("users/". $userId ."/manager", [ 'managerId' => $managerId ], null, 'PUT');
            break;
        case USER_TAG_ASSOCIATION_REQUEST:
            echo '
                <pre>
                $data = $shwApi->doRequest("users/" . $userId . "/associateTags",
                    array("isIdOrigin" => 1),
                    array("tags" => array($tagId)),
                    "PUT"
                );
                </pre>
            ';
            break;
        case USER_TAG_DISSOCIATION_REQUEST:
            echo '
                <pre>
                $data = $shwApi->doRequest("users/" . $userId . "/dissociateTags",
                    array("isIdOrigin" => 1),
                    array("tags" => array($tagId)),
                    "PUT"
                );
                </pre>
            ';
                break;
        case TAGS_REQUEST:
            $data = $shwApi->doRequest("tags");
            break;
        case TAG_REQUEST:
            $data = $shwApi->doRequest("tags/". $tagId);
            break;
        case TAG_USERS_REQUEST:
            $data = $shwApi->doRequest("tags/". $tagId ."/users");
            break;
        case DYB_USERS_LASTUPDATE:
            $data = $shwApi->doRequest("dyb/users/lastupdate", array('since' => '@1'));
            break;
        case DYB_USERS_CREATED:
            $data = $shwApi->doRequest("dyb/users/created", array('since' => '@1'));
            break;
        case DYB_USERS_LASTDELETE:
            $data = $shwApi->doRequest("dyb/users/lastdelete", array('since' => '@1'));
            break;
        case DYB_USER_REQUEST:
            $data = $shwApi->doRequest("dyb/user/".$userId, array('isIdOrigin' => 1));
            break;
        case DYB_CV_REQUEST:
            $data = $shwApi->doRequest("dyb/cv/".$cvId);
            break;
        case DYB_CV_LIST:
            $filters = [ "filters" => [
                [ "type" => "cvType", "value" => "main" ]
            ]];
            $filtersJson = json_encode($filters);
            $data = $shwApi->doRequest("cv/list", [], $filtersJson);
            break;
        case DYB_EMPLOYMENT_PREFERENCES_REQUEST:
            $data = $shwApi->doRequest("dyb/employmentpreferences/". $userId, array('isIdOrigin' => 1));
            break;
        case DYB_SEARCH_REQUEST:
            $searchRequest = '
                <search>
                    <queries>
                        <query>
                            <term>PHP</term>
                            <fields>
                                <in>cv</in>
                                <in>jobs</in>
                                <in>educations</in>
                            </fields>
                        </query>
                    </queries>
                    <filters>
                        <filter>
                        </filter>
                    </filters>
                </search>
            ';
            $data = $shwApi->doRequest("dyb/search", array(), $searchRequest);
            break;
        case DYB_METADATA_REQUEST:
            $metadatas = '
                <metadatas>
                    <users>
                        <user>' . $userId . '</user>
                    </users>
                    <unassign>
                        <metadatas>
                            <metadata>
                                <key>com.acmeinc.user.kind:*</key>
                            </metadata>
                        </metadatas>
                    </unassign>
                    <assign>
                        <metadatas>
                            <metadata>
                                <key>com.acmeinc.user.kind:awesome-guy</key>
                            </metadata>
                        </metadatas>
                    </assign>
                </metadatas>
            ';
            $data = $shwApi->doRequest("dyb/metadata/update", array(), $metadatas);
            break;

        case DYB_CV_DISPLAY_CONFIG:
            $data = $shwApi->doRequest(sprintf("dyb/cv/%s/display/%s", $cvId, "web"));
            break;
    }
    if ($data) { echo json_encode($data); }




    /**
     * displayHomePage
     * @access public
     * @return void
     */
    function displayHomePage($requestTranslation)
    {
        ?>
        <html>
            <head>
                <style>
                    a { display:block; }
                </style>
            </head>
            <body>
            <?php
                foreach( $requestTranslation as $requestKey => $requestName) {
                    echo '<a href="/?request=' . $requestKey . '">' . $requestName . '</a>
                    ';
                }
            ?>
            </body>
        </html>
        <?php
    }
