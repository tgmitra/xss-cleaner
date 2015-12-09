<?php

# Include security class
include 'security.class.php';

# Initiate filter_payload
$filter_payload = new filter_payload();

# Clean all request payload
$filter_payload->clean_request_payload();

