export const parsePercent = (str) => {
    if (str.indexOf("%") === -1) return ["", str]       // если нет "%" - возврат ["", исходная строка]

    return [str.substring(0, str.indexOf("%") + 1), str.substring(str.indexOf("%") + 2, str.length)]
}