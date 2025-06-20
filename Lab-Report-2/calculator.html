<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        
        .calculator {
            width: 380px; /* Increased from 300px */
            background-color: #8e8e8e;
            border-radius: 12px;
            padding: 20px; /* Increased padding */
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3); /* Enhanced shadow */
        }
        
        .display {
            background-color: #f8f8f8;
            border-radius: 8px;
            padding: 20px; /* Increased padding */
            margin-bottom: 20px; /* Increased margin */
            text-align: right;
            min-height: 100px; /* Increased from 80px */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .previous-operand {
            color: #777;
            font-size: 18px; /* Increased font size */
            min-height: 25px; /* Increased height */
        }
        
        .current-operand {
            color: #333;
            font-size: 40px; /* Increased font size */
            min-height: 50px; /* Increased height */
        }
        
        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 15px; /* Increased gap */
        }
        
        button {
            border: none;
            outline: none;
            padding: 20px 10px; /* Increased padding */
            font-size: 22px; /* Increased font size */
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        button:active {
            transform: scale(0.95);
        }
        
        .digit, .operation, .equal, .decimal {
            background-color: #FF8000; /* Pure orange */
            color: white;
        }
        
        .digit:hover, .operation:hover, .equal:hover, .decimal:hover {
            background-color: #FF9933; /* Slightly lighter orange on hover */
        }
        
        .operation, .equal {
            font-weight: bold;
        }
        
        .special {
            grid-column: span 2;
        }
        
        .clear {
            background-color: #ffcaca;
            color: #ff4040;
        }
        
        .clear:hover {
            background-color: #ffb0b0;
        }
        
        .delete {
            background-color: #d0d0ff;
            color: #5050ff;
        }
        
        .delete:hover {
            background-color: #b8b8ff;
        }
    </style>
</head>
<body>
    <div class="calculator">
        <div class="display">
            <div class="previous-operand"></div>
            <div class="current-operand">0</div>
        </div>
        <div class="buttons">
            <button class="clear special">CLEAR</button>
            <button class="delete special">DELETE</button>
            
            <button class="digit">7</button>
            <button class="digit">8</button>
            <button class="digit">9</button>
            <button class="operation">÷</button>
            
            <button class="digit">4</button>
            <button class="digit">5</button>
            <button class="digit">6</button>
            <button class="operation">×</button>
            
            <button class="digit">1</button>
            <button class="digit">2</button>
            <button class="digit">3</button>
            <button class="operation">-</button>
            
            <button class="decimal">.</button>
            <button class="digit">0</button>
            <button class="equal">=</button>
            <button class="operation">+</button>
        </div>
    </div>
    
    <script>
        // Calculator functionality
        class Calculator {
            constructor(previousOperandElement, currentOperandElement) {
                this.previousOperandElement = previousOperandElement;
                this.currentOperandElement = currentOperandElement;
                this.clear();
            }
            
            clear() {
                this.currentOperand = '0';
                this.previousOperand = '';
                this.operation = undefined;
            }
            
            delete() {
                this.currentOperand = this.currentOperand.toString().slice(0, -1);
                if (this.currentOperand === '') {
                    this.currentOperand = '0';
                }
            }
            
            appendNumber(number) {
                if (number === '.' && this.currentOperand.includes('.')) return;
                if (this.currentOperand === '0' && number !== '.') {
                    this.currentOperand = number;
                } else {
                    this.currentOperand = this.currentOperand.toString() + number.toString();
                }
            }
            
            chooseOperation(operation) {
                if (this.currentOperand === '') return;
                if (this.previousOperand !== '') {
                    this.compute();
                }
                this.operation = operation;
                this.previousOperand = this.currentOperand;
                this.currentOperand = '';
            }
            
            compute() {
                let computation;
                const prev = parseFloat(this.previousOperand);
                const current = parseFloat(this.currentOperand);
                if (isNaN(prev) || isNaN(current)) return;
                
                switch (this.operation) {
                    case '+':
                        computation = prev + current;
                        break;
                    case '-':
                        computation = prev - current;
                        break;
                    case '×':
                        computation = prev * current;
                        break;
                    case '÷':
                        computation = prev / current;
                        break;
                    default:
                        return;
                }
                
                this.currentOperand = computation;
                this.operation = undefined;
                this.previousOperand = '';
            }
            
            getDisplayNumber(number) {
                const stringNumber = number.toString();
                const integerDigits = parseFloat(stringNumber.split('.')[0]);
                const decimalDigits = stringNumber.split('.')[1];
                let integerDisplay;
                
                if (isNaN(integerDigits)) {
                    integerDisplay = '';
                } else {
                    integerDisplay = integerDigits.toLocaleString('en', { maximumFractionDigits: 0 });
                }
                if (decimalDigits != null) {
                    return `${integerDisplay}.${decimalDigits}`;
                } else {
                    return integerDisplay;
                }
            }
            
            updateDisplay() {
                this.currentOperandElement.innerText = this.getDisplayNumber(this.currentOperand);
                if (this.operation != null) {
                    this.previousOperandElement.innerText = 
                        `${this.getDisplayNumber(this.previousOperand)} ${this.operation}`;
                } else {
                    this.previousOperandElement.innerText = '';
                }
            }
        }
        
        // DOM elements
        const previousOperandElement = document.querySelector('.previous-operand');
        const currentOperandElement = document.querySelector('.current-operand');
        const digitButtons = document.querySelectorAll('.digit');
        const operationButtons = document.querySelectorAll('.operation');
        const equalsButton = document.querySelector('.equal');
        const clearButton = document.querySelector('.clear');
        const deleteButton = document.querySelector('.delete');
        const decimalButton = document.querySelector('.decimal');
        
        // Create calculator instance
        const calculator = new Calculator(previousOperandElement, currentOperandElement);
        
        // Add event listeners
        digitButtons.forEach(button => {
            button.addEventListener('click', () => {
                calculator.appendNumber(button.innerText);
                calculator.updateDisplay();
            });
        });
        
        operationButtons.forEach(button => {
            button.addEventListener('click', () => {
                calculator.chooseOperation(button.innerText);
                calculator.updateDisplay();
            });
        });
        
        equalsButton.addEventListener('click', () => {
            calculator.compute();
            calculator.updateDisplay();
        });
        
        clearButton.addEventListener('click', () => {
            calculator.clear();
            calculator.updateDisplay();
        });
        
        deleteButton.addEventListener('click', () => {
            calculator.delete();
            calculator.updateDisplay();
        });
        
        decimalButton.addEventListener('click', () => {
            calculator.appendNumber('.');
            calculator.updateDisplay();
        });
    </script>
</body>
</html>