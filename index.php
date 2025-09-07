<?php
require 'vendor/autoload.php';

use Aws\Ec2\Ec2Client;
use Aws\Exception\AwsException;

// Step 1: Configure AWS Client
$ec2Client = new Ec2Client([
    'region'      => 'us-east-1',             // Change as needed
    'version'     => 'latest',
    'credentials' => [
        'key'    => 'YOUR_AWS_ACCESS_KEY',   // Replace with your key
        'secret' => 'YOUR_AWS_SECRET_KEY',   // Replace with your secret
    ],
]);

// Step 2: Create EBS Volume
try {
    $createVolume = $ec2Client->createVolume([
        'AvailabilityZone' => 'us-east-1a',  // Make sure this matches your EC2
        'Size'             => 20,            // Volume size in GiB
        'VolumeType'       => 'gp3',         // General Purpose SSD
    ]);

    $volumeId = $createVolume['VolumeId'];
    echo "✅ EBS Volume Created Successfully! Volume ID: {$volumeId}<br>";

    // Wait until volume becomes available (optional, but recommended)
    $ec2Client->waitUntil('VolumeAvailable', [
        'VolumeIds' => [$volumeId],
    ]);
    echo "ℹ️ Volume is now available.<br>";

} catch (AwsException $e) {
    echo "❌ Error creating EBS volume: " . $e->getMessage() . "<br>";
    exit;
}

// Step 3: Attach EBS Volume to EC2 Instance
$instanceId = 'i-0123456789abcdef0';  // Replace with your actual Instance ID

try {
    $attachVolume = $ec2Client->attachVolume([
        'Device'     => '/dev/sdf',   // Device name
        'InstanceId' => $instanceId,
        'VolumeId'   => $volumeId,
    ]);

    echo "✅ EBS Volume {$volumeId} Attached Successfully to Instance {$instanceId}.<br>";

} catch (AwsException $e) {
    echo "❌ Error attaching EBS volume: " . $e->getMessage() . "<br>";
    exit;
}
?>
