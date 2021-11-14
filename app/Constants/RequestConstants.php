<?php

namespace App\Constants;

/**
 *
 */
abstract class RequestConstants
{

    public const STATUS_CODES = [
        "success" => 200,
        "not_found" => 404,
        "duplicate" => 422,
        "validation" => 400,
        "unauthorized" => 403
    ];

    public const RESPONSES = [
        "fetched_go" => "RSS Feeds successfully fetched!",
        "created" => "Item Created Successfully!",
        "updated" => "Item Updated Successfully!",
        "deleted" => "Item Deleted Successfully!",
        "not_found" => "Item Not Found!",
        "not_found_go" => "No RSS Feeds Are Available!",
        "duplicate" => "An Item With The Same Parameters Already Exists!",
        "unauthorized" => "Invalid login details"
    ];

    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';
}
