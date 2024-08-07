<?php
session_start();

// Database connection parameters
include('kk.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $sessionUsername = $_POST['name'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $bodyfat = $_POST['bodyfat'];
    $visceralfat = $_POST['visceralfat'];
    $RMR = $_POST['RMR'];
    $BMI = $_POST['BMI'];
    $subcutfat = $_POST['subcutfat'];
    $skeletmusc = $_POST['skeletmusc'];
    $BFM = $_POST['BFM'];
    $sugar = $_POST['sugar'];
    $BP = $_POST['BP'];

    // Update user details in the database
    $sqlUpdate = "UPDATE users SET height = ?, weight = ?, bodyfat = ?, visceralfat = ?, RMR = ?, BMI = ?, subcutfat = ?, skeletmusc = ?, BFM = ?, sugar = ?, BP = ? WHERE username = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);

    if ($stmtUpdate) {
        $stmtUpdate->bind_param("ssssssssssss", $height, $weight, $bodyfat, $visceralfat, $RMR, $BMI, $subcutfat, $skeletmusc, $BFM, $sugar, $BP, $sessionUsername);
        $stmtUpdateResult = $stmtUpdate->execute();

        if ($stmtUpdateResult === false) {
            echo "Error executing update statement: " . $stmtUpdate->error;
        } else {
            $stmtUpdate->close();
            // Successfully executed the update statement
            // echo "Details updated successfully!";
        }
    } else {
        echo "Error preparing update statement: " . $conn->error;
    }

    // Assuming your "ideal" table has columns like height, idealweight, idealbodyfat, etc.
    $sqlIdeal = "SELECT * FROM ideal WHERE height >= ? ORDER BY height DESC LIMIT 1";
    $stmtIdeal = $conn->prepare($sqlIdeal);

    if ($stmtIdeal === false) {
        echo "Error preparing ideal values query: " . $conn->error;
    } else {
        $stmtIdeal->bind_param("d", $height);
        $stmtIdeal->execute();
        $resultIdeal = $stmtIdeal->get_result();

        if ($resultIdeal->num_rows > 0) {
            $rowIdeal = $resultIdeal->fetch_assoc();

            // Assign fetched ideal values to corresponding variables
            $idealWeight = $rowIdeal['idealweight'];
            $idealBodyFat = $rowIdeal['idealbodyfat'];
            $idealVisceralFat = $rowIdeal['idealvisceralfat'];
            $idealRMR = $rowIdeal['idealRMR'];
            $idealBMI = $rowIdeal['idealBMI'];
            $idealSubcutaneousFat = $rowIdeal['idealsubcutfat'];
            $idealSkeletalMuscle = $rowIdeal['idealskeletmusc'];
            $idealBFM = $rowIdeal['idealBFM'];
        } else {
            echo "No ideal values found for the given height.";
        }
        $stmtIdeal->close();
    }
} else {
    // Set default values if the form is not submitted
    $sugar = '';  // Set a default value for sugar
    $BP = '';     // Set a default value for BP
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Add this to your existing styles or in a style block in the head */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .tab {
            max-width: 1100px; /* Adjust the maximum width as needed */
            width: 100%;
            padding: 20px;
          margin-top: 120px;
            /* Add a subtle shadow */
            justify-content: center;
            height: 170vh;
            max-height: 80%;
            margin-bottom: 40px;
        }

        /* Rest of your existing styles */
        .item {
            margin-bottom: 20px; /* Adjust the margin as needed */
        }
.no{
    width:20%;

}
        label {
            display: inline-block;
            margin-bottom: 10px; /* Adjust the margin as needed */
        }

        input, select {
            width: 40%; /* Adjust the width as needed */
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .column {
            width: 30%;
        }
        .small-name {
            width: 20%; /* Adjust the width as needed */
        }
       
        .subs{
        display: block;
  width: 70px;
  background: rgba(255, 111, 0);
 
  padding: 8px;
  margin: 5px auto;
  border-radius: 5px;
  cursor: pointer;
  border: 2px solid ;  
    }
        </style>
</head>
<body>

<div class="tab">
    <form class="items" method="post" action=""   id="submitForm">
        <div class="item">
            <label for="name">User Name:</label>
            <input type="text" name="name" id="name" class="small-name" value="<?php echo $sessionUsername ?? ''; ?>"  required>
        </div>
        <div class="item row">
            <div class="column">
            <label for="height">Height:</label>
            <input type="number" name="height" id="height" value="<?php echo $height ?? ''; ?>" required>
    </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="weight">Weight:</label>
            <input type="number" name="weight" id="weight" value="<?php echo $weight ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealweight">Ideal Weight:</label>
            <input type="text" name="idealweight" id="idealweight" value="<?php echo $idealWeight ?? ''; ?>" required readonly>
            </div>
            <div class="column">
            <label for="targetweight">Target:</label>
            <input type="text" name="idealweight" id="targetweight" >
            </div>
        </div>
        <div class="itemn row">
        <div class="column">
            <label for="bodyfat">Body Fat:</label>
            <input type="number" name="bodyfat" id="bodyfat" value="<?php echo $bodyfat ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealbodyfat">Ideal Body Fat:</label>
            <input type="text" name="idealbodyfat" id="idealbodyfat" value="<?php echo $idealBodyFat ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetbodyfat">Target:</label>
            <input type="text" name="targetbodyfat" id="targetbodyfat" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="visceralfat">Visceral Fat Level:</label>
            <input type="number" name="visceralfat" id="visceralfat" value="<?php echo $visceralfat ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealvisceralfat">Ideal Visceral Fat:</label>
            <input type="text" name="idealvisceralfat" id="idealvisceralfat" value="<?php echo $idealVisceralFat ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetvisceralfat">Target:</label>
            <input type="text" name="targetvisceralfat" id="targetvisceralfat" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="RMR">RMR:</label>
            <input type="number" name="RMR" id="RMR" value="<?php echo $RMR ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealRMR">Ideal RMR:</label>
            <input type="text" name="idealRMR" id="idealRMR" value="<?php echo $idealRMR ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetRMR">Target:</label>
            <input type="text" name="targetRMR" id="targetRMR" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="BMI">BMI:</label>
            <input type="number" name="BMI" id="BMI" value="<?php echo $BMI ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealBMI">Ideal BMI:</label>
            <input type="text" name="idealBMI" id="idealBMI" value="<?php echo $idealBMI ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetBMI">Target:</label>
            <input type="text" name="targetBMI" id="targetBMI" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="subcutfat">Subcutaneous Fat:</label>
            <input type="number" name="subcutfat" id="subcutfat" value="<?php echo $subcutfat ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealsubcutfat">Ideal Subcutaneous Fat:</label>
            <input type="text" name="idealsubcutfat" id="idealsubcutfat" value="<?php echo $idealSubcutaneousFat ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetsubcutfat">Target:</label>
            <input type="text" name="targetsubcutfat" id="targetsubcutfat" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="skeletmusc">Skeletal Muscle:</label>
            <input type="number" name="skeletmusc" id="skeletmusc" value="<?php echo $skeletmusc ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealskeletmusc">Ideal Skeletal Muscle:</label>
            <input type="text" name="idealskeletmusc" id="idealskeletmusc" value="<?php echo $idealSkeletalMuscle ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetskeletmusc">Target:</label>
            <input type="text" name="targetskeletmusc" id="targetskeletmusc" required readonly>
            </div>
        </div>
        <div class="item row">
        <div class="column">
            <label for="BFM">BFM:</label>
            <input type="number" name="BFM" id="BFM" value="<?php echo $BFM ?? ''; ?>" required>
            </div>
            <div class="column">
            <label for="idealBFM">Ideal BFM:</label>
            <input type="text" name="idealBFM" id="idealBFM" value="<?php echo $idealBFM ?? ''; ?>">
            </div>
            <div class="column">
            <label for="targetBFM">Target:</label>
            <input type="text" name="targetBFM" id="targetBFM" required readonly>
            </div>
        </div>
        <div class="item">
        <label for="sugar">Sugar:</label>
        <select id="sugar" name="sugar" class="no">
        <option></option>
        <option <?php if ($sugar == 'Yes') echo 'selected'; ?> value="Yes" id="yes" name="yes">Yes</option>
        <option  <?php if ($sugar == 'No') echo 'selected'; ?> value="No" id="no" name="no">No</option>
        </select>
    </div>
        <div class="item">
        <label for="BP">BP:</label>
        <select id="BP" name="BP" class="no">
        <option></option>
        <option value="Yes" <?php if ($BP == 'Yes') echo 'selected'; ?> id="yes" name="yes" >Yes</option>
        <option value="No" <?php if ($BP == 'No') echo 'selected'; ?> id="no" name="no">No</option>
        </select>
    </div>
    <button type="submit" value="submit" class="sub" id="submit">Submit</button> 
    </form>
</div>
<script>
    function updateTargetValues() {
        // Get height and ideal height values
       
        // Get weight and ideal weight values
        var weight = parseFloat(document.getElementById('weight').value) || 0;
        var idealWeight = parseFloat(document.getElementById('idealweight').value) || 0;

        // Calculate and update target weight
        var targetWeight =idealWeight- weight;
        document.getElementById('targetweight').value = targetWeight;

         // Get weight and ideal body fat values
         var bodyfat = parseFloat(document.getElementById('bodyfat').value) || 0;
        var idealbodyfat = parseFloat(document.getElementById('idealbodyfat').value) || 0;

        // Calculate and update target weight
        var targetbodyfat = idealbodyfat - bodyfat;
        document.getElementById('targetbodyfat').value = targetbodyfat;
        // Get visceral fat and ideal visceral fat values
        var visceralFat = parseFloat(document.getElementById('visceralfat').value) || 0;
        var idealVisceralFat = parseFloat(document.getElementById('idealvisceralfat').value) || 0;

        // Calculate and update target visceral fat
        var targetVisceralFat = idealVisceralFat - visceralFat;
        document.getElementById('targetvisceralfat').value = targetVisceralFat;

        // Get RMR and ideal RMR values
        var RMR = parseFloat(document.getElementById('RMR').value) || 0;
        var idealRMR = parseFloat(document.getElementById('idealRMR').value) || 0;

        // Calculate and update target RMR
        var targetRMR = idealRMR-RMR;
        document.getElementById('targetRMR').value = targetRMR;

        // Get BMI and ideal BMI values
        var BMI = parseFloat(document.getElementById('BMI').value) || 0;
        var idealBMI = parseFloat(document.getElementById('idealBMI').value) || 0;

        // Calculate and update target BMI
        var targetBMI = idealBMI - BMI;
        document.getElementById('targetBMI').value = targetBMI;

        // Get subcutaneous fat and ideal subcutaneous fat values
        var subcutaneousFat = parseFloat(document.getElementById('subcutfat').value) || 0;
        var idealSubcutaneousFat = parseFloat(document.getElementById('idealsubcutfat').value) || 0;

        // Calculate and update target subcutaneous fat
        var targetSubcutaneousFat = idealSubcutaneousFat - subcutaneousFat;
        document.getElementById('targetsubcutfat').value = targetSubcutaneousFat;
     // Get skeletal muscle and ideal skeletal muscle values
     var skeletalMuscle = parseFloat(document.getElementById('skeletmusc').value) || 0;
        var idealSkeletalMuscle = parseFloat(document.getElementById('idealskeletmusc').value) || 0;

        // Calculate and update target skeletal muscle
        var targetSkeletalMuscle =idealSkeletalMuscle -  skeletalMuscle;
        document.getElementById('targetskeletmusc').value = targetSkeletalMuscle;

        // Get BFM and ideal BFM values
        var BFM = parseFloat(document.getElementById('BFM').value) || 0;
        var idealBFM = parseFloat(document.getElementById('idealBFM').value) || 0;

        // Calculate and update target BFM
        var targetBFM = idealBFM - BFM;
        document.getElementById('targetBFM').value = targetBFM;
    }
    updateTargetValues(); 
    //document.getElementById('submitForm').submit();
    
</script>

</body>
</html>
