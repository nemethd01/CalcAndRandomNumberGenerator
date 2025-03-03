<?php
$charMax = 1000;
?>

<form style="margin-top: 1.5rem" action="gyakorlas2.php" method="get">
    <label for="inputNumber">Adjon meg egy számot! (Max <?= $charMax ?>)</label>
    <br>
    <input
        style="margin-top: 0.5rem"
        id="inputNumber"
        name="inputNumber"
        type="text"
    >
    <button type="submit" value="value">Küldés</button>
</form>
<?php
$inputValue = $_GET['inputNumber'] ?? '';
$inputValue = preg_replace('/[^0-9]+/', '', $inputValue);

$filteredValue = [];
$errorMsg = '<p style="color: red">A megadott szám minimum 1, maximum '.$charMax.' lehet!</p>';
$inputValueInt = intval($inputValue);
if ($inputValueInt<=$charMax && $inputValueInt > 0){
    for ($i = 0; $i < $inputValueInt; $i++){
        $result[] = rand(1, $inputValueInt);
    }
    function odd($inputValueItem){
        return !($inputValueItem % 2 == 0);
    }
    print '<pre>';
    $filteredValue = array_filter($result, "odd");
    print '</pre>';

} else {
    print $errorMsg;
}

if (count($filteredValue) > 0){
    foreach ($filteredValue as $value){
        ?>
        <div> <?= $value ?></div>
<?php
    }
}


//×××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××××


$calcInputValue = $_GET['calcInput'] ?? '';
$calcInputHidden = $_GET['calcInputHidden'] ?? '';
$calcNumbers = explode(",", $calcInputHidden);
$preCalc = implode(' ', $calcNumbers);
$preCalcNoneSpace = implode('', $calcNumbers);
$pattern = '/^([0-9]+[+*\/-])+[0-9]+$/';
//$pattern = '/^[\d+\-\/\*]+$/';
//$pattern = '/^(?!.*[+\-*\/]{2})[0-9+*\/-]+$/';
$inputPattern = '/^([0-9]+)$|^([+\/*-])$/';
$validPattern = false;

//print $calcInputHidden;

if ($calcInputValue){
    if (!preg_match($inputPattern, $calcInputValue)){
        print '<p style="color: red">A beírt szám/műveleti jel formátuma nem megfelelő!</p>';
    } else {
        if ($calcInputValue !== ''){
            if (!$calcInputHidden){
                $calcInputHidden = $calcInputValue;
            } else {
                $calcInputHidden = $calcInputHidden.','.$calcInputValue;
            }
        }
    }
}

if (isset($_GET['resultButton'])){
    if (!preg_match($pattern, $preCalcNoneSpace)){
        print '<p style="color: red">A képlet formátuma nem megfelelő!</p>';
    } else {
        $validPattern = true;
    }
}

if (isset($_GET['deleteOne'])){
    array_pop($calcNumbers);
    $calcInputHidden = implode(',', $calcNumbers);
}

//eredmény kiszámolása

//$example = 10 * 10 - 5 / 2 * 10 * 5 / 2;

if (isset($_GET['resultButton'])) {
    function muveletek(&$numberArray):array|null {
        $multiplicationIndex = array_search("*", $numberArray);
        $divisionIndex = array_search("/", $numberArray);
        $operatorIndex = false;

        if ($multiplicationIndex !== false && !$divisionIndex) {
            $operatorIndex = $multiplicationIndex;

        } elseif ($divisionIndex !== false && !$multiplicationIndex) {
            $operatorIndex = $divisionIndex;

        } else{
            if ($multiplicationIndex !== false && $divisionIndex !== false) {
                $operatorIndex = ($multiplicationIndex < $divisionIndex) ? $multiplicationIndex : $divisionIndex;
            }
        }
        if ($operatorIndex !== false){
            $prewValue = $numberArray[$operatorIndex - 1];
            $nextValue = $numberArray[$operatorIndex + 1];
            $operator = $numberArray[$operatorIndex];

            if ($operator === "*"){
                $numberArray[$operatorIndex - 1] = $prewValue * $nextValue;

            } else {
                if ($nextValue == 0) {
                    print "Nullával való osztást nem értelmezünk!";
                    return null;
                } else {
                    $numberArray[$operatorIndex -1] = $prewValue / $nextValue;
                }
            }
            array_splice($numberArray, $operatorIndex, 2);
            muveletek($numberArray);
        }
        return $numberArray;
    }
    $calcNumbers = muveletek($calcNumbers);
}

$result = 0;
$operator = '';

foreach ($calcNumbers as $value){
    if (is_numeric($value)) {
        switch ($operator) {
            case '+':
                $result += $value;
                break;
            case '-':
                $result -= $value;
                break;
            /*case '*':
                $result *= $value;
                break;
            case '/':
                if ($value != 0) {
                    $result /= $value;
                } else {
                    print 'Nullával való osztást nem értelmezünk';
                }
                break;*/
            default:
                $result = $value;
                break;
        }
    } else {
        $operator = $value;
    }
}

?>
<form style="margin-top: 5rem" action="gyakorlas2.php" method="get">
    <label for="calc">Számológép</label>
    <br>
    <input
        style="margin-top: 0.5rem"
        id="calc"
        type="text"
        name="calcInput"
    >
    <button type="submit" value="value">Hozzáad</button>
    <button type="submit" name="resultButton" value="result">Számol</button>
    <input
        type="hidden"
        name="calcInputHidden"
        value="<?= $calcInputHidden ?>"
    >
    <button type="submit" name="deleteOne" value="delete">Egy törlése</button>
</form>
<form action="gyakorlas2.php" method="get">
    <button type="submit" name="resetButton" value="reset">Nullázó</button>
</form>
<?php
if (isset($_GET['resultButton'])){
    if ($validPattern){
        print $preCalc.'<br>';
        print "Az eredmény: $result";
    }
}

?>
