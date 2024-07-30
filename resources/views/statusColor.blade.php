@php
    function GetStatusColor($status){
        switch ($status) {
            case 'Under_Revision':
                echo 'background-color: #ffcccb;';
                break;
            case 'Under_Finalization':
                echo 'background-color: #c0c916;';
                break;
            case 'Approved':
                echo 'background-color: #90ee90;';
                break;
            case 'Rejected':
                echo 'background-color: #ff0000;';
                break;
            default:
                echo 'background-color: #ffcccb;';
        }
    }
@endphp
