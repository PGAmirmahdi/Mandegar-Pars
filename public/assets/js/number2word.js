class Number2Word {
    constructor() {
        this.digit1 = {
            0: 'صفر',
            1: 'یک',
            2: 'دو',
            3: 'سه',
            4: 'چهار',
            5: 'پنج',
            6: 'شش',
            7: 'هفت',
            8: 'هشت',
            9: 'نه',
        };
        this.digit1_5 = {
            1: 'یازده',
            2: 'دوازده',
            3: 'سیزده',
            4: 'چهارده',
            5: 'پانزده',
            6: 'شانزده',
            7: 'هفده',
            8: 'هجده',
            9: 'نوزده',
        };
        this.digit2 = {
            1: 'ده',
            2: 'بیست',
            3: 'سی',
            4: 'چهل',
            5: 'پنجاه',
            6: 'شصت',
            7: 'هفتاد',
            8: 'هشتاد',
            9: 'نود',
        };
        this.digit3 = {
            1: 'صد',
            2: 'دویست',
            3: 'سیصد',
            4: 'چهارصد',
            5: 'پانصد',
            6: 'ششصد',
            7: 'هفتصد',
            8: 'هشتصد',
            9: 'نهصد',
        };
        this.steps = {
            1: 'هزار',
            2: 'میلیون',
            3: 'بیلیون',
            4: 'تریلیون',
            5: 'کادریلیون',
            6: 'کوینتریلیون',
            7: 'سکستریلیون',
            8: 'سپتریلیون',
            9: 'اکتریلیون',
            10: 'نونیلیون',
            11: 'دسیلیون',
        };
        this.t = {
            and: 'و',
        };
    }

    number_format(number, decimal_precision = 0, decimals_separator = '.', thousands_separator = ',') {
        number = number !== '' ? number.toString().split('.') : '0'.toString().split('.');
        number[0] = number[0].split('').reverse().join('').match(/.{1,3}/g).map(segment => segment.split('').reverse().join('')).reverse().join(thousands_separator);
        if (number[1]) {
            number[1] = parseFloat('0.' + number[1]).toFixed(decimal_precision);
        }
        return number.join(decimals_separator);
    }

    groupToWords(group) {
        const d3 = Math.floor(group / 100);
        const d2 = Math.floor((group - d3 * 100) / 10);
        const d1 = group - d3 * 100 - d2 * 10;
        const group_array = [];
        if (d3 !== 0) {
            group_array.push(this.digit3[d3]);
        }
        if (d2 === 1 && d1 !== 0) {
            group_array.push(this.digit1_5[d1]);
        } else if (d2 !== 0 && d1 === 0) {
            group_array.push(this.digit2[d2]);
        } else if (d2 === 0 && d1 === 0) {
            // Do nothing for 00
        } else if (d2 === 0 && d1 !== 0) {
            group_array.push(this.digit1[d1]);
        } else {
            group_array.push(this.digit2[d2]);
            group_array.push(this.digit1[d1]);
        }
        if (group_array.length === 0) {
            return false;
        }
        return group_array;
    }

    numberToWords(number) {
        const formated = this.number_format(number, 0, '.', ',');
        const groups = formated.split(',');
        const steps = groups.length;
        const parts = [];
        groups.forEach((group, step) => {
            const group_words = this.groupToWords(parseInt(group, 10));
            if (group_words) {
                let part = group_words.join(` ${this.t.and} `);
                if (this.steps[steps - step - 1]) {
                    part += ` ${this.steps[steps - step - 1]}`;
                }
                parts.push(part);
            }
        });
        return parts.join(` ${this.t.and} `);
    }
}
