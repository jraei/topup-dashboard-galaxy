export const toRupiah = (value) => {
    let number = Number(value);
    if (isNaN(number)) return "Rp 0";

    return "Rp " + number.toLocaleString("id-ID");
};
